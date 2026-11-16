<?php
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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class template extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  public static function cron() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */
  
  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  /*
   * Permet d'indiquer des éléments supplémentaires à remonter dans les informations de configuration
   * lors de la création semi-automatique d'un post sur le forum community
   public static function getConfigForCommunity() {
      // Cette function doit retourner des infos complémentataires sous la forme d'un
      // string contenant les infos formatées en HTML.
      return "les infos essentiel de mon plugin";
   }
   */

  /*     * ***************************Démon******************************* */
  /* À supprimer si le plugin n'a pas de démon (hasOwnDeamon à false dans info.json). */

  public static function deamon_info() {
    $return = array();
    $return['log'] = __CLASS__;
    $return['state'] = 'nok';
    $pid_file = jeedom::getTmpFolder(__CLASS__) . '/deamon.pid';
    if (file_exists($pid_file) && @posix_getsid(trim(file_get_contents($pid_file)))) {
      $return['state'] = 'ok';
    }
    $return['launchable'] = 'ok';
    return $return;
  }

  public static function deamon_start() {
    self::deamon_stop();
    $deamon_info = self::deamon_info();
    if ($deamon_info['launchable'] != 'ok') {
      throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
    }
    // Python de l'environnement du plugin, créé par l'installation des dépendances (section pip3 de packages.json).
    $python = is_dir(__DIR__ . '/../../resources/python_venv') ? system::getCmdPython3(__CLASS__) : 'python3 ';
    $cmd = $python . escapeshellarg(realpath(__DIR__ . '/../../resources/demond') . '/demond.py');
    $cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel(__CLASS__));
    $cmd .= ' --socket ' . escapeshellarg(self::daemonSocketPath());
    $cmd .= ' --cycle ' . escapeshellarg(config::byKey('cycle', __CLASS__, 0.3));
    $cmd .= ' --pid ' . escapeshellarg(jeedom::getTmpFolder(__CLASS__) . '/deamon.pid');
    $cmd .= self::daemonJeedomArguments();
    log::add(__CLASS__, 'info', 'Lancement du démon');
    exec($cmd . ' >> ' . log::getPathToLog(__CLASS__ . '_daemon') . ' 2>&1 &');
    for ($i = 0; $i < 20; $i++) {
      if (self::deamon_info()['state'] == 'ok') {
        message::removeAll(__CLASS__, 'unableStartDeamon');
        return true;
      }
      sleep(1);
    }
    log::add(__CLASS__, 'error', __('Impossible de lancer le démon, vérifiez le log', __FILE__), 'unableStartDeamon');
    return false;
  }

  public static function deamon_stop() {
    $pid_file = jeedom::getTmpFolder(__CLASS__) . '/deamon.pid';
    if (file_exists($pid_file)) {
      system::kill(intval(trim(file_get_contents($pid_file))));
    }
    system::kill('demond.py');
    if (file_exists(self::daemonSocketPath())) {
      unlink(self::daemonSocketPath());
    }
  }

  /*
   * Socket Unix du démon. Seul Jeedom peut l'ouvrir : le PHP n'a pas besoin de clé API pour parler au démon.
   */
  public static function daemonSocketPath() {
    return jeedom::getTmpFolder(__CLASS__) . '/daemon.sock';
  }

  /*
   * Arguments qui permettent au démon d'envoyer ses messages à Jeedom :
   * - Jeedom 5 : API REST, avec un jeton propre au démon qu'il renouvelle lui-même ;
   * - Jeedom 4 : core/php/jeetemplate.php, avec la clé API du plugin.
   */
  private static function daemonJeedomArguments() {
    $url = network::getNetworkAccess('internal', 'http:127.0.0.1:port:comp');
    if (method_exists('plugin', 'daemonCredentialFile')) {
      try {
        $credentials = plugin::daemonCredentialFile(__CLASS__);
      } catch (DomainException $e) {
        // Session du démon supprimée par Jeedom (expirée ou révoquée) : on lui en ouvre une nouvelle.
        $credentials = plugin::daemonCredentialFile(__CLASS__, true);
      }
      config::save('daemon::transport', 'rest', __CLASS__);
      return ' --callback ' . escapeshellarg($url . '/api/v1/plugins/' . __CLASS__ . '/daemon/messages')
        . ' --rest-credentials ' . escapeshellarg($credentials);
    }
    config::save('daemon::transport', 'callback', __CLASS__);
    return ' --callback ' . escapeshellarg($url . '/plugins/' . __CLASS__ . '/core/php/jee' . __CLASS__ . '.php')
      . ' --apikey ' . escapeshellarg(jeedom::getApiKey(__CLASS__));
  }

  /*
   * Envoie un message au démon (reçu dans read_socket() de demond.py).
   * Exemple : template::sendToDaemon(array('action' => 'test'));
   *
   * Avec un démon Node.js (jeedom.js, Jeedom.http.config(socket)), le PHP envoie une requête HTTP sur le socket :
   *   $request = new com_http('http://localhost/action');
   *   $request->setCURLOPT(array(CURLOPT_UNIX_SOCKET_PATH => self::daemonSocketPath()));
   *   $request->setHeader(array('Content-Type: application/json'));
   *   $request->setPost(json_encode($params));
   *   $request->exec();
   */
  public static function sendToDaemon($params) {
    $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
    if ($socket === false || !@socket_connect($socket, self::daemonSocketPath())) {
      throw new Exception(__('Le démon ne répond pas', __FILE__));
    }
    $payload = json_encode($params) . "\n";
    socket_write($socket, $payload, strlen($payload));
    socket_close($socket);
  }

  /*
   * Messages envoyés par le démon avec jeedom_com, regroupés par clé :
   * add_changes('equipement::temperature', 21.5) donne array('equipement' => array('temperature' => 21.5)).
   * Appelée par Jeedom 5 et, avec Jeedom 4, par core/php/jeetemplate.php.
   */
  public static function handleDaemonMessages(array $messages) {
    log::add(__CLASS__, 'debug', 'Messages du démon : ' . json_encode($messages));
  }

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*     * **********************Getteur Setteur*************************** */
}

class templateCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
  }

  /*     * **********************Getteur Setteur*************************** */
}
