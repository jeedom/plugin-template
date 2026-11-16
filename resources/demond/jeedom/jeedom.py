# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
#

import time
import logging
import fcntl
import json
import secrets
from datetime import datetime
from threading import Thread, Lock
import requests
from collections.abc import Mapping
import serial
import os
from queue import Queue
import socketserver
from socketserver import (TCPServer, StreamRequestHandler)
import unicodedata
import pyudev


class jeedom_rest_access():
    """Jeton du démon pour l'API REST de Jeedom 5.

    Le fichier transmis par le PHP (--rest-credentials) contient un jeton de renouvellement. Il est échangé
    contre un jeton d'accès valable 24 h et un nouveau jeton de renouvellement, enregistré à sa place.
    """

    def __init__(self, path, messages_url):
        self._path = path
        self._refresh_url = messages_url.split('/api/v1/')[0] + '/api/v1/session/refresh'
        self._token = None
        self._expires = 0
        self._lock = Lock()

    def token(self):
        with self._lock:
            if self._token is None or self._expires < time.time() + 5:
                self._refresh()
            return self._token

    def forget(self):
        self._token = None

    def _refresh(self):
        # Même verrou que plugin::daemonCredentialFile() côté PHP.
        with open(self._path + '.lock', 'a') as lock:
            fcntl.flock(lock, fcntl.LOCK_EX)
            with open(self._path) as file:
                credentials = json.load(file)
            # La clé est enregistrée avant l'appel : si la réponse se perd, le prochain essai la réutilise
            # et Jeedom renvoie la même paire au lieu de croire à un vol du jeton.
            attempt = credentials.get('refreshAttempt') or secrets.token_hex(16)
            self._store({'refreshToken': credentials['refreshToken'], 'refreshAttempt': attempt})
            response = requests.post(self._refresh_url, json={'refreshToken': credentials['refreshToken']},
                                     headers={'Idempotency-Key': attempt}, timeout=10, verify=False)
            if response.status_code in (401, 403):
                raise jeedom_access_lost('Accès REST du démon refusé par Jeedom (HTTP %s)' % response.status_code)
            response.raise_for_status()
            data = response.json()
            self._store({'refreshToken': data['refreshToken']})
            self._token = data['accessToken']
            self._expires = datetime.fromisoformat(data['accessExpiresAt'].replace('Z', '+00:00')).timestamp()

    def _store(self, credentials):
        temp = self._path + '.tmp'
        with open(os.open(temp, os.O_WRONLY | os.O_CREAT | os.O_TRUNC, 0o600), 'w') as file:
            json.dump(credentials, file)
        os.replace(temp, self._path)


class jeedom_access_lost(Exception):
    """Jeedom a supprimé l'accès du démon : seul un redémarrage par Jeedom lui en redonne un."""


class jeedom_com():
    """Envoi des messages du démon au plugin, reçus par sa méthode handleDaemonMessages().

    Jeedom 4 : url est core/php/jee<plugin>.php et apikey la clé API du plugin.
    Jeedom 5 : url est /api/v1/plugins/<plugin>/daemon/messages et credentials le fichier --rest-credentials.
    """

    def __init__(self, apikey='', url='', cycle=0.5, retry=3, credentials=''):
        self._apikey = apikey
        self._url = url
        self._cycle = cycle
        self._retry = retry
        self._changes = {}
        self._rest = jeedom_rest_access(credentials, url) if credentials else None
        if self._cycle > 0:
            Thread(target=self.__thread_changes_async, daemon=True).start()
        logging.info('Init request module v%s', requests.__version__)

    def __thread_changes_async(self):
        if self._cycle <= 0:
            return
        logging.info('Start changes async thread')
        while True:
            try:
                time.sleep(self._cycle)
                if len(self._changes) == 0:
                    continue
                changes = self._changes
                self._changes = {}
                self.__post_change(changes)
            except Exception as error:
                logging.error('Critical error on send_changes_async %s', error)

    def add_changes(self, key: str, value):
        if key.find('::') != -1:
            tmp_changes = {}
            changes = value
            for k in reversed(key.split('::')):
                if k not in tmp_changes:
                    tmp_changes[k] = {}
                tmp_changes[k] = changes
                changes = tmp_changes
                tmp_changes = {}
            if self._cycle <= 0:
                self.send_change_immediate(changes)
            else:
                self.merge_dict(self._changes, changes)
        else:
            if self._cycle <= 0:
                self.send_change_immediate({key: value})
            else:
                self._changes[key] = value

    def send_change_immediate(self, change):
        Thread(target=self.__post_change, args=(change,)).start()

    def __post_change(self, change):
        logging.debug('Send to jeedom: %s', change)
        for i in range(self._retry):
            try:
                if self.__post(change):
                    return True
            except jeedom_access_lost as error:
                logging.error('%s : arrêt du démon, Jeedom lui redonnera un accès au redémarrage', error)
                os._exit(1)
            except Exception as error:
                logging.error('Error on send request to jeedom "%s" retry: %i/%i', error, i, self._retry)
            time.sleep(0.5)
        return False

    def __post(self, change):
        if self._rest is None:
            r = requests.post(self._url + '?apikey=' + self._apikey, json=change, timeout=(0.5, 120), verify=False)
            expected = requests.codes.ok
        else:
            r = requests.post(self._url, json={'messages': change}, timeout=(0.5, 120), verify=False,
                              headers={'Authorization': 'Bearer ' + self._rest.token()})
            if r.status_code == 401:
                self._rest.forget()  # jeton expiré : le prochain essai en demande un nouveau
            expected = 204
        if r.status_code != expected:
            logging.warning('Error on send request to jeedom, return code %s', r.status_code)
        return r.status_code == expected

    def set_change(self, changes):
        self._changes = changes

    def get_change(self):
        return self._changes

    def merge_dict(self, d1, d2):
        for k, v2 in d2.items():
            v1 = d1.get(k)  # returns None if v1 has no value for this key
            if isinstance(v1, Mapping) and isinstance(v2, Mapping):
                self.merge_dict(v1, v2)
            else:
                d1[k] = v2

    def test(self):
        if self._rest is not None:
            try:
                return self.__post({})
            except Exception as e:
                logging.error('Jeedom REST API error: %s', e)
                return False
        try:
            response = requests.get(self._url + '?apikey=' + self._apikey, verify=False)
            if response.status_code != requests.codes.ok:
                logging.error('Callback error: %s %s. Please check your network configuration page', response.status_code, response.reason)
                return False
        except Exception as e:
            logging.error('Callback result as a unknown error: %s. Please check your network configuration page', e)
            return False
        return True


class jeedom_utils():

    @staticmethod
    def convert_log_level(level='error'):
        LEVELS = {
            'debug': logging.DEBUG,
            'info': logging.INFO,
            'notice': logging.WARNING,
            'warning': logging.WARNING,
            'error': logging.ERROR,
            'critical': logging.CRITICAL,
            'none': logging.CRITICAL
            }
        return LEVELS.get(level, logging.CRITICAL)

    @staticmethod
    def set_log_level(level='error'):
        FORMAT = '[%(asctime)-15s][%(levelname)s] : %(message)s'
        logging.basicConfig(level=jeedom_utils.convert_log_level(level), format=FORMAT, datefmt="%Y-%m-%d %H:%M:%S")

    @staticmethod
    def find_tty_usb(idVendor, idProduct, product=None):
        context = pyudev.Context()
        for device in context.list_devices(subsystem='tty'):
            if 'ID_VENDOR' not in device:
                continue
            if device['ID_VENDOR_ID'] != idVendor:
                continue
            if device['ID_MODEL_ID'] != idProduct:
                continue
            if product is not None:
                if 'ID_VENDOR' not in device or device['ID_VENDOR'].lower().find(product.lower()) == -1:
                    continue
            return str(device.device_node)
        return None

    @staticmethod
    def stripped(str):
        # Le socket fournit des octets : ils sont décodés, puis les caractères de contrôle sont retirés.
        if isinstance(str, bytes):
            str = str.decode('utf-8', errors='ignore')
        return "".join([i for i in str if ord(i) >= 32])

    @staticmethod
    def ByteToHex(byteStr: bytes):
        return byteStr.hex()

    @staticmethod
    def dec2bin(x, width=8):
        return ''.join(str((x >> i) & 1) for i in range(width-1, -1, -1))

    @staticmethod
    def dec2hex(dec):
        if dec is None:
            return '0x00'
        return "0x{:02X}".format(dec)

    @staticmethod
    def testBit(int_type, offset):
        mask = 1 << offset
        return (int_type & mask)

    @staticmethod
    def clearBit(int_type, offset):
        mask = ~(1 << offset)
        return (int_type & mask)

    @staticmethod
    def split_len(seq, length):
        return [seq[i:i+length] for i in range(0, len(seq), length)]

    @staticmethod
    def write_pid(path):
        pid = str(os.getpid())
        logging.info("Writing PID %s to %s", pid, path)
        open(path, 'w').write("%s\n" % pid)

    @staticmethod
    def remove_accents(input_str: str):
        nkfd_form = unicodedata.normalize('NFKD', input_str)
        return u"".join([c for c in nkfd_form if not unicodedata.combining(c)])

    @staticmethod
    def printHex(hex):
        return ' '.join([hex[i:i + 2] for i in range(0, len(hex), 2)])


class jeedom_serial():

    def __init__(self, device='', rate='', timeout=9, rtscts=True, xonxoff=False):
        self.device = device
        self.rate = rate
        self.timeout = timeout
        self.port = None
        self.rtscts = rtscts
        self.xonxoff = xonxoff
        logging.info('Init serial module v%s', serial.VERSION)

    def open(self):
        if self.device:
            logging.info("Open serial port on device: %s, rate %s, timeout: %i", self.device, self.rate, self.timeout)
        else:
            logging.error("Device name missing.")
            return False
        logging.info("Open Serialport")
        try:
            self.port = serial.Serial(
                self.device,
                self.rate,
                timeout=self.timeout,
                rtscts=self.rtscts,
                xonxoff=self.xonxoff,
                parity=serial.PARITY_NONE,
                stopbits=serial.STOPBITS_ONE
            )
        except serial.SerialException as e:
            logging.error("Error: Failed to connect on device %s. Details : %s", self.device, e)
            return False
        if not self.port.isOpen():
            self.port.open()
        self.flushOutput()
        self.flushInput()
        return True

    def close(self):
        logging.info("Close serial port")
        try:
            self.port.close()
            logging.info("Serial port closed")
            return True
        except serial.SerialException as e:
            logging.error("Failed to close the serial port (%s): %s", self.device, e)
            return False

    def write(self, data):
        logging.info("Write data to serial port: %s", str(jeedom_utils.ByteToHex(data)))
        self.port.write(data)

    def flushOutput(self,):
        logging.info("flushOutput serial port ")
        self.port.flushOutput()

    def flushInput(self):
        logging.info("flushInput serial port ")
        self.port.flushInput()

    def read(self):
        if self.port.inWaiting() != 0:
            return self.port.read()
        return None

    def readbytes(self, number):
        buf = b''
        for i in range(number):
            try:
                byte = self.port.read()
            except IOError as e:
                logging.error("Error: %s", e)
            except OSError as e:
                logging.error("Error: %s", e)
            buf += byte
        return buf


JEEDOM_SOCKET_MESSAGE = Queue()


class jeedom_socket_handler(StreamRequestHandler):
    def handle(self):
        global JEEDOM_SOCKET_MESSAGE
        lg = self.rfile.readline()
        JEEDOM_SOCKET_MESSAGE.put(lg)
        logging.debug("Message read from socket: %s", str(lg.strip()))
        self.netAdapterClientConnected = False


class jeedom_socket():
    """Réception des messages du PHP. Avec path, le démon écoute sur un socket Unix que seul Jeedom peut
    ouvrir : plus besoin de clé API ni de port. Sans path, ancien fonctionnement sur un port TCP local :
    le démon doit alors vérifier la clé API de chaque message."""

    def __init__(self, address='localhost', port=55000, path=None):
        self.address = address
        self.port = port
        self.path = path
        socketserver.TCPServer.allow_reuse_address = True

    def open(self):
        if self.path:
            if os.path.exists(self.path):
                os.remove(self.path)
            os.umask(0o077)  # socket accessible au seul compte de Jeedom
            self.netAdapter = socketserver.ThreadingUnixStreamServer(self.path, jeedom_socket_handler)
            self.address, self.port = self.path, 0
        else:
            self.netAdapter = TCPServer((self.address, self.port), jeedom_socket_handler)
        if self.netAdapter:
            logging.info("Socket interface started")
            Thread(target=self.loopNetServer).start()
        else:
            logging.info("Cannot start socket interface")

    def loopNetServer(self):
        logging.info("LoopNetServer Thread started")
        logging.info("Listening on: %s", self.path or '%s:%d' % (self.address, self.port))
        self.netAdapter.serve_forever()
        logging.info("LoopNetServer Thread stopped")

    def close(self):
        self.netAdapter.shutdown()

    def getMessage(self):
        return self.message
