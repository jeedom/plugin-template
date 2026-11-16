/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

const axios = require('axios');
var express = require('express');
const http = require('http');
const fs = require('fs');
const crypto = require('crypto');

axios.defaults.httpAgent = new http.Agent({ keepAlive: false });

var Jeedom = {}
Jeedom.log = {}
Jeedom.com = {}
Jeedom.http = {}

/***************************ARGS*******************************/

Jeedom.getArgs = function() {
  var result = {}
  var args = process.argv.slice(2,process.argv.length);
  for (var i = 0, len = args.length; i < len; i++) {
    if (args[i].slice(0,2) === '--') {
      result[args[i].slice(2,args[i].length)] = args[i + 1]
    }
  }
  return result
}

/***************************LOGS*******************************/

Jeedom.log.setLevel = function(_level){
  var convert = {debug  : 0,info : 10,notice : 20,warning : 30,error : 40,critical : 50,none : 60}
  Jeedom.log.level = convert[_level]
}

Jeedom.log.debug  = function(_log){
  if(Jeedom.log.level > 0){
    return;
  }
  console.log('['+(new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''))+'][DEBUG] : '+_log)
}

Jeedom.log.info  = function(_log){
  if(Jeedom.log.level > 10){
    return;
  }
  console.log('['+(new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''))+'][INFO] : '+_log)
}

Jeedom.log.error  = function(_log){
  if(Jeedom.log.level > 40){
    return;
  }
  console.log('['+(new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''))+'][ERROR] : '+_log)
}

/***************************PID*******************************/

Jeedom.write_pid = function(_file){
  var fs = require('fs');
  fs.writeFile(_file, process.pid.toString(), function(err) {
    if(err) {
      Jeedom.log.error("Can't write pid file : "+err);
      process.exit()
    }
  });
}

/***************************COM*******************************/

Jeedom.isObject = function(item) {
  return (item && typeof item === 'object' && !Array.isArray(item));
}

// Les clés viennent de l'extérieur (topics, équipements) : elles sont écrites comme de simples données,
// pour qu'une clé __proto__ ou constructor ne modifie jamais les objets du démon.
Jeedom.setKey = function(target, key, value) {
  Object.defineProperty(target, key, { value: value, writable: true, enumerable: true, configurable: true });
}

Jeedom.mergeDeep = function(target, ...sources) {
  if (!sources.length) return target;
  const source = sources.shift();
  if (Jeedom.isObject(target) && Jeedom.isObject(source)) {
    for (const key of Object.keys(source)) {
      if (Jeedom.isObject(source[key])) {
        if (!Object.prototype.hasOwnProperty.call(target, key) || !Jeedom.isObject(target[key])) Jeedom.setKey(target, key, {});
        Jeedom.mergeDeep(target[key], source[key]);
      } else {
        Jeedom.setKey(target, key, source[key]);
      }
    }
  }
  return Jeedom.mergeDeep(target, ...sources);
}

/*
 * Jeedom 4 : _callback est core/php/jee<plugin>.php et _apikey la clé API du plugin.
 * Jeedom 5 : _callback est /api/v1/plugins/<plugin>/daemon/messages et _credentials le fichier --rest-credentials.
 * Les messages arrivent dans la méthode handleDaemonMessages() du plugin.
 */
Jeedom.com.config = function(_apikey,_callback,_cycle,_credentials){
  Jeedom.com.apikey = _apikey;
  Jeedom.com.callback = _callback;
  Jeedom.com.cycle = _cycle;
  Jeedom.com.credentials = _credentials;
  Jeedom.com.changes = {};
  if(Jeedom.com.cycle > 0){
    setInterval(function() {
      if(Object.keys(Jeedom.com.changes).length > 0){
        Jeedom.com.send_change_immediate(Jeedom.com.changes);
        Jeedom.com.changes = {};
      }
    }, Jeedom.com.cycle * 1000);
  }
}

Jeedom.com.add_changes = function(_key,_value){
  // 'a::b' devient { a: { b: valeur } }
  var changes = _value
  for (const key of _key.split('::').reverse()) {
    var parent = {}
    Jeedom.setKey(parent, key, changes)
    changes = parent
  }
  if (Jeedom.com.cycle <= 0){
    Jeedom.com.send_change_immediate(changes)
  }else{
    Jeedom.com.changes = Jeedom.mergeDeep(Jeedom.com.changes,changes)
  }
}

Jeedom.com.send_change_immediate = function(_changes){
  Jeedom.log.debug('Send data to jeedom : '+JSON.stringify(_changes));
  Jeedom.com.post(_changes).catch(function (error) {
    Jeedom.log.error('Error on send to jeedom : '+error.message);
  })
}

Jeedom.com.post = async function(_changes){
  if (!Jeedom.com.credentials) {
    return axios({
      method : 'POST',
      url:Jeedom.com.callback+'?apikey='+Jeedom.com.apikey,
      data: JSON.stringify(_changes)
    })
  }
  const send = async function() {
    return axios.post(Jeedom.com.callback, JSON.stringify({ messages: _changes }), {
      headers: { Authorization: 'Bearer ' + await Jeedom.rest.token(), 'Content-Type': 'application/json' },
      validateStatus: () => true,
    })
  }
  let response = await send()
  if (response.status === 401) {
    Jeedom.rest.forget() // jeton expiré : on en demande un nouveau
    response = await send()
  }
  if (response.status === 401 || response.status === 403) {
    Jeedom.log.error('Accès REST du démon refusé : arrêt, Jeedom lui redonnera un accès au redémarrage')
    process.exit(1)
  }
  if (response.status !== 204) throw new Error('HTTP ' + response.status)
}

Jeedom.com.test = function(){
  const check = Jeedom.com.credentials ? Jeedom.com.post({}) : axios({
    method:'GET',
    url:Jeedom.com.callback+'?apikey='+Jeedom.com.apikey
  })
  check.catch(function (error) {
    Jeedom.log.error('Callback error.Please check your network configuration page : '+error.message);
    process.exit();
  })
}

/***************************REST (Jeedom 5)*******************************/

/*
 * Le fichier transmis par le PHP (--rest-credentials) contient un jeton de renouvellement. Il est échangé
 * contre un jeton d'accès valable 24 h et un nouveau jeton de renouvellement, enregistré à sa place.
 */
Jeedom.rest = {}

Jeedom.rest.token = async function(){
  if (Jeedom.rest.accessToken && Jeedom.rest.expiresAt > Date.now() + 5000) {
    return Jeedom.rest.accessToken
  }
  if (!Jeedom.rest.pending) {
    Jeedom.rest.pending = Jeedom.rest.refresh().finally(function() { Jeedom.rest.pending = null })
  }
  return Jeedom.rest.pending
}

Jeedom.rest.forget = function(){
  Jeedom.rest.accessToken = null
}

Jeedom.rest.refresh = async function(){
  const file = Jeedom.com.credentials
  const credentials = JSON.parse(fs.readFileSync(file, 'utf8'))
  // La clé est enregistrée avant l'appel : si la réponse se perd, le prochain essai la réutilise
  // et Jeedom renvoie la même paire au lieu de croire à un vol du jeton.
  const attempt = credentials.refreshAttempt || crypto.randomBytes(16).toString('hex')
  Jeedom.rest.store({ refreshToken: credentials.refreshToken, refreshAttempt: attempt })
  const url = Jeedom.com.callback.split('/api/v1/')[0] + '/api/v1/session/refresh'
  const response = await axios.post(url, { refreshToken: credentials.refreshToken }, {
    headers: { 'Idempotency-Key': attempt },
    validateStatus: () => true,
  })
  if (response.status === 401 || response.status === 403) {
    Jeedom.log.error('Accès REST du démon refusé : arrêt, Jeedom lui redonnera un accès au redémarrage')
    process.exit(1)
  }
  if (response.status !== 200) throw new Error('Renouvellement du jeton : HTTP ' + response.status)
  Jeedom.rest.store({ refreshToken: response.data.refreshToken })
  Jeedom.rest.accessToken = response.data.accessToken
  Jeedom.rest.expiresAt = Date.parse(response.data.accessExpiresAt)
  return Jeedom.rest.accessToken
}

Jeedom.rest.store = function(credentials){
  const temp = Jeedom.com.credentials + '.tmp'
  fs.writeFileSync(temp, JSON.stringify(credentials), { mode: 0o600 })
  fs.renameSync(temp, Jeedom.com.credentials)
}

/***************************HTTP SERVER*******************************/

/*
 * Serveur HTTP qui reçoit les demandes du PHP.
 * Avec un chemin (--socket), il écoute sur un socket Unix que seul Jeedom peut ouvrir : plus besoin de clé API.
 * Avec un numéro de port, ancien fonctionnement sur 127.0.0.1 avec la clé API.
 */
Jeedom.http.config = function(_port,_apikey){
  Jeedom.http.apikey = _apikey;
  Jeedom.http.socket = typeof _port === 'string' && _port.startsWith('/');
  Jeedom.http.app = express();
  Jeedom.http.app.use(express.urlencoded({limit: '5mb', extended: true }));
  Jeedom.http.app.use(express.json({limit: '5mb'}));
  Jeedom.http.app.get('/', function(req, res) {
    res.setHeader('Content-Type', 'text/plain');
    res.status(404).send('Not found');
  });
  if (Jeedom.http.socket) {
    if (fs.existsSync(_port)) fs.unlinkSync(_port);
    process.umask(0o077); // socket accessible au seul compte de Jeedom
    Jeedom.http.app.listen(_port, function() {
      Jeedom.log.debug('HTTP listen on socket ' + _port + ' started');
    });
    return;
  }
  Jeedom.http.app.listen(_port,'127.0.0.1', function() {
    Jeedom.log.debug('HTTP listen on 127.0.0.1 port : '+_port+' started');
  });
}

Jeedom.http.checkApikey = function(_req){
  return Jeedom.http.socket || (_req.query.apikey === Jeedom.http.apikey)
}

/***************************EXPORTS*******************************/

exports.getArgs = Jeedom.getArgs;
exports.log = Jeedom.log;
exports.write_pid = Jeedom.write_pid;
exports.com = Jeedom.com;
exports.http = Jeedom.http;
