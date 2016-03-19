De loin un des dossiers le plus important de votre plugin, il peut comporter 4 sous dossiers. 

Note : tous le long de cette partie l'id de votre plugin sera referencé par : plugin_id

===== PHP

Contient les fichiers PHP annexe, j'ai pris l'habitude de mettre par exemple un fichier d'inclusion si, bien sur, vous avez plusieurs fichiers de class ou des 3rparty à inclure

===== Template

Qui peut contenir 2 sous-dossiers, dashboard et mobile, c'est un dossier que Jeedom scanne automatiquement à la recherche de widget, donc si vous utilisez des widgets specifiques c'est ici qu'il faut mettre leur fichier html

===== i18n

C'est ici que votre traduction doit aller sous forme de fichier json (le mieux et de regarder par exemple le plugin link:https://github.com/jeedom/plugin-zwave[zwave] pour voir la forme du fichier)

===== ajax

Ce dossier est pour tout vos fichier ajax, voici un squelette de fichier ajax : 

----

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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'votre action') {
       
        ajax::success($result);
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>

----


==== class

Dossier très important c'est le moteur de votre plugin, c'est la que viennent les 2 classes obligatoires de votre plugin : 

- plugin_id
- plugin_idCmd

La premiere devant hériter de la classe eqLogic et la deuxieme de cmd. Voici un template : 

----
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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class plugin_id extends eqLogic {

    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    
    /*     * **********************Getteur Setteur*************************** */

}

class plugin_idCmd extends cmd {

    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    
    /*     * **********************Getteur Setteur*************************** */

}

?>
----

Pour la definition des classes jeedom, je vous invite à consulter ce link:http://dev.jeedom.fr/[site]

La seule methode obligatoire est la methode d'instance sur la classe cmd execute, voici un exemple avec le plugin S.A.R.A.H : 

----

 public function execute($_options = array()) {
        if (!isset($_options['title']) && !isset($_options['message'])) {
            throw new Exception(__("Le titre ou le message ne peuvent être tous les deux vide", __FILE__));
        }
        $eqLogic = $this->getEqLogic();
        $message = '';
        if (isset($_options['title'])) {
            $message = $_options['title'] . '. ';
        }
        $message .= $_options['message'];
        $http = new com_http($eqLogic->getConfiguration('addrSrvTts') . '/?tts=' . urlencode($message));
        return $http->exec();
    }

----

Exemple assez simple mais complet, le principe est le suivant, si la commande est une action ou une info (mais pas en evenement seulement et que son cache est dépassé) alors jeedom appelle cette méthode.

Dans notre exemple ici c'est une commande pour faire parler S.A.R.A.H, où le plugin recupère les paramètres dans $_options (attention c'est un tableau et ses attributs changent en fonction du sous-type de la commande : color pour un sous-type color, slider pour un sous-type slider, title et message pour un sous-type message et vide pour un sous-type other).

Voila pour la partie obligatoire, voila maintenant ce qui peut etre utilisé à coté (avec exemple) : 

.toHtml($_version = 'dashboard')

Fonction utilisable dans la commande ou dans l'équipement, en fonction des besoins, voici un exemple pour l'équipement

----

   public function toHtml($_version = 'dashboard') {
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $version = jeedom::versionAlias($_version);
        $replace['#forecast#'] = '';
        if ($version != 'mobile' || $this->getConfiguration('fullMobileDisplay', 0) == 1) {
            $forcast_template = getTemplate('core', $version, 'forecast', 'weather');
            for ($i = 0; $i < 5; $i++) {
                $replaceDay = array();
                $replaceDay['#day#'] = date_fr(date('l', strtotime('+' . $i . ' days')));

                if ($i == 0) {
                    $temperature_min = $this->getCmd(null, 'temperature_min');
                } else {
                    $temperature_min = $this->getCmd(null, 'temperature_' . $i . '_min');
                }
                $replaceDay['#low_temperature#'] = is_object($temperature_min) ? $temperature_min->execCmd() : '';

                if ($i == 0) {
                    $temperature_max = $this->getCmd(null, 'temperature_max');
                } else {
                    $temperature_max = $this->getCmd(null, 'temperature_' . $i . '_max');
                }
                $replaceDay['#hight_temperature#'] = is_object($temperature_max) ? $temperature_max->execCmd() : '';
                $replaceDay['#tempid#'] = is_object($temperature_max) ? $temperature_max->getId() : '';

                if ($i == 0) {
                    $condition = $this->getCmd(null, 'condition');
                } else {
                    $condition = $this->getCmd(null, 'condition_' . $i);
                }
                $replaceDay['#icone#'] = is_object($condition) ? self::getIconFromCondition($condition->execCmd()) : '';
                $replaceDay['#conditionid#'] = is_object($condition) ? $condition->getId() : '';
                $replace['#forecast#'] .= template_replace($replaceDay, $forcast_template);
            }
        }
        $temperature = $this->getCmd(null, 'temperature');
        $replace['#temperature#'] = is_object($temperature) ? $temperature->execCmd() : '';
        $replace['#tempid#'] = is_object($temperature) ? $temperature->getId() : '';

        $humidity = $this->getCmd(null, 'humidity');
        $replace['#humidity#'] = is_object($humidity) ? $humidity->execCmd() : '';

        $pressure = $this->getCmd(null, 'pressure');
        $replace['#pressure#'] = is_object($pressure) ? $pressure->execCmd() : '';
        $replace['#pressureid#'] = is_object($pressure) ? $pressure->getId() : '';

        $wind_speed = $this->getCmd(null, 'wind_speed');
        $replace['#windspeed#'] = is_object($wind_speed) ? $wind_speed->execCmd() : '';
        $replace['#windid#'] = is_object($wind_speed) ? $wind_speed->getId() : '';

        $sunrise = $this->getCmd(null, 'sunrise');
        $replace['#sunrise#'] = is_object($sunrise) ? $sunrise->execCmd() : '';
        $replace['#sunid#'] = is_object($sunrise) ? $sunrise->getId() : '';
        if (strlen($replace['#sunrise#']) == 3) {
            $replace['#sunrise#'] = substr($replace['#sunrise#'], 0, 1) . ':' . substr($replace['#sunrise#'], 1, 2);
        } else if (strlen($replace['#sunrise#']) == 4) {
            $replace['#sunrise#'] = substr($replace['#sunrise#'], 0, 2) . ':' . substr($replace['#sunrise#'], 2, 2);
        }

        $sunset = $this->getCmd(null, 'sunset');
        $replace['#sunset#'] = is_object($sunset) ? $sunset->execCmd() : '';
        if (strlen($replace['#sunset#']) == 3) {
            $replace['#sunset#'] = substr($replace['#sunset#'], 0, 1) . ':' . substr($replace['#sunset#'], 1, 2);
        } else if (strlen($replace['#sunset#']) == 4) {
            $replace['#sunset#'] = substr($replace['#sunset#'], 0, 2) . ':' . substr($replace['#sunset#'], 2, 2);
        }

        $wind_direction = $this->getCmd(null, 'wind_direction');
        $replace['#wind_direction#'] = is_object($wind_direction) ? $wind_direction->execCmd() : 0;

        $refresh = $this->getCmd(null, 'refresh');
        $replace['#refresh_id#'] = is_object($refresh) ? $refresh->getId() : '';

        $condition = $this->getCmd(null, 'condition_now');
        $sunset_time = is_object($sunset) ? $sunset->execCmd() : null;
        $sunrise_time = is_object($sunrise) ? $sunrise->execCmd() : null;
        if (is_object($condition)) {
            $replace['#icone#'] = self::getIconFromCondition($condition->execCmd(), $sunrise_time, $sunset_time);
            $replace['#condition#'] = $condition->execCmd();
            $replace['#conditionid#'] = $condition->getId();
            $replace['#collectDate#'] = $condition->getCollectDate();
        } else {
            $replace['#icone#'] = '';
            $replace['#condition#'] = '';
            $replace['#collectDate#'] = '';
        }
        if ($this->getConfiguration('modeImage', 0) == 1) {
            $replace['#visibilityIcon#'] = "none";
            $replace['#visibilityImage#'] = "block";
        } else {
            $replace['#visibilityIcon#'] = "block";
            $replace['#visibilityImage#'] = "none";
        }
        $html = template_replace($replace, getTemplate('core', $version, 'current', 'weather'));
        cache::set('widgetHtml' . $_version . $this->getId(), $html, 0);
        return $html;
    }

----
 
Plusieurs choses interessantes ici : 

Pour convertir la version demandée en dashboard ou mobile (mview devient mobile par exemple, cela permet par exemple sur les vues de rajouter le nom des objets)
----
$_version = jeedom::versionAlias($_version);
----

Ici recuperation du widget anciennement generé en cache (si celui-ci est non vide), ça permet de gagner du temps sur la generation, attention quand meme à bien vider le cache lors de la mise à jour des données
----
   $mc = cache::byKey('netatmoWeatherWidget' . jeedom::versionAlias($_version) . $this->getId());
    if ($mc->getValue() != '') {
        return preg_replace("/" . preg_quote(self::UIDDELIMITER) . "(.*?)" . preg_quote(self::UIDDELIMITER) . "/", self::UIDDELIMITER . mt_rand() . self::UIDDELIMITER, $mc->getValue());
    }
----

Recuperation d'un template de commande, ici le template de commande : plugins/weather/core/template/$_version/forecast.html ($_version valant mobile ou dashboard)
----
$forcast_template = getTemplate('core', $_version, 'forecast', 'weather');
----

Ici remplacement des tag prealablement remplis dans $replace du html pour avoir les valeurs dessus
----
$html_forecast .= template_replace($replace, $forcast_template);
----

Cela permet de recuperer la commande ayant le logical_id : temperature_min
----
$this->getCmd(null, 'temperature_min');
----

La ça permet de mettre la valeur dans le tag, seulement si la commande a bien été récuperée
----
$replace['#temperature#'] = is_object($temperature) ? $temperature->execCmd() : '';
----

Passage important, ça permet de recuperer les personalisations faites par l'utilisateur sur la page Générale -> Affichage et de les reinjecter dans le template
----
$parameters = $this->getDisplay('parameters');
if (is_array($parameters)) {
    foreach ($parameters as $key => $value) {
        $replace['#' . $key . '#'] = $value;
    }
}
----

Sauvegarde du widget dans le cache, pour que lors de la prochaine demande on le fournisse plus rapidement, on peut remarquer le 0 ici qui indique une durée de vie infinie, sinon la durée est en secondes (on verra dans la partie suivante comment le plugin weather remet à jour son widget).
----
cache::set('weatherWidget' . $_version . $this->getId(), $html, 0);
----

Enfin envoi du html à Jeedom : 
----
return $html;
----

Il faut aussi dire à jeedom ce que votre widget autorise au niveau de la personalisation.Le truc est un peu complexe (et encore) mais normalement flexible et simple a mettre en place.

Il marche pareil sur votre équipement ou commande, c'est un attribut static de la class $_widgetPossibility qui doit être un tableau multidimensionnel, mais c'est la que ca se complique si une dimension du tableau est a true ou false alors il considère que tout les enfants possible sont à cette valeur (je vais donner un exemple).

Déja les cas ou vous devez vous en servir : si dans votre class heritant de eqLogic ou de cmd à une fonction toHtml sinon pas la peine de lire la suite.

Vue que le mieux c'est un exemple (dans la class heritant de eqLogic) :

----
public static $_widgetPossibility = array('custom' => array(
      'visibility' => true,
      'displayName' => array('dashboard' => true, 'view' => true),
      'optionalParameters' => true,
));
----

En gros la ca dit que l'on peut changer la visibilité du widget, masquer ou non le nom de celui-ci et mettre des paramètres optionnels.

Mais j'aurais aussi bien pu faire : 

----
public static $_widgetPossibility = array('custom' => array(
      'visibility' => true,
      'displayName' => true,
      'optionalParameters' => true,
));
----


La différence est au niveau du displayName, la si jeedom demande si on peut masquer le nom de l'équipement en mode vue (ca donne custom::displayName::view) on lui dira oui car custom::displayName est vrai donc tous les enfants de ca sont vrai

Voila pour l'explication, en ce qui concerne les possibilités les voila pour un équipement : 

----
array('custom' => 
   array(
      'visibility' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'displayName' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'displayObjectName' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'optionalParameters' => true/false,
      'background-color' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'text-color' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'border-radius' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'border' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
   ),
)
----

Pour une commande : 

----
array('custom' => 
   array(
      'widget' => array('dashboard' => true/false,'mobile' => true/false),
      'displayName' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'displayObjectName' => array('dashboard' => true/false,'plan' => true/false,'view' => true/false,'mobile' => true/false),
      'optionalParameters' => true/false,
   ),
)
----

.methode pre et post
Lors de la creation ou la supression de vos objet (equipement, commande ou autre) dans Jeedom, celui-ci peut appeler plusieurs méthodes avant/après l'action : 

- preInsert => Méthode appellée avant la création de votre objet
- postInsert =>  Méthode appellée après la création de votre objet
- preUpdate =>  Méthode appellée avant la mise à jour de votre objet
- postUpdate =>  Méthode appellée après la mise à jour de votre objet
- preSave =>  Méthode appellée avant la sauvegarde (creation et mise à jour donc) de votre objet
- postSave =>  Méthode appellée après la sauvegarde de votre objet
- preRemove =>  Méthode appellée avant la supression de votre objet
- postRemove =>  Méthode appellée après la supression de votre objet

Exemple, toujours avec le plugin weather de la creation des commandes ou mise à jour de celle-ci après la sauvegarde (l'exemple est simplifié) : 

----
 public function postUpdate() {
        $weatherCmd = $this->getCmd(null, 'temperature');
        if (!is_object($weatherCmd)) {
            $weatherCmd = new weatherCmd();
        }
        $weatherCmd->setName(__('Température', __FILE__));
        $weatherCmd->setLogicalId('temperature');
        $weatherCmd->setEqLogic_id($this->getId());
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'temp');
        $weatherCmd->setUnite('°C');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $cron = cron::byClassAndFunction('weather', 'updateWeatherData', array('weather_id' => intval($this->getId())));
        if (!is_object($cron)) {
            $cron = new cron();
            $cron->setClass('weather');
            $cron->setFunction('updateWeatherData');
            $cron->setOption(array('weather_id' => intval($this->getId())));
        }
        $cron->setSchedule($this->getConfiguration('refreshCron', '*/30 * * * *'));
        $cron->save();
}
----

Le début est assez standard avec la création d'une commande, la fin est plus interessante avec la mise en place d'un cron qui va appeler la methode weather::updateWeatherData en passant l'id de l'équipement à mettre à jour toute les 30min par défaut.

Ici la methode updateWeatherData (simplifiée aussi) : 
----
 public static function updateWeatherData($_options) {
    $weather = weather::byId($_options['weather_id']);
    if (is_object($weather)) {
        foreach ($weather->getCmd('info') as $cmd) {
            $value = $cmd->execute();
            if ($value != $cmd->execCmd()) {
                $cmd->setCollectDate('');
                $cmd->event($value);
            }
        }
        $mc = cache::byKey('weatherWidgetmobile' . $weather->getId());
        $mc->remove();
        $mc = cache::byKey('weatherWidgetdashboard' . $weather->getId());
        $mc->remove();
        $weather->toHtml('mobile');
        $weather->toHtml('dashboard');
        $weather->refreshWidget();
    }
}
----

On voit ici que lors de l'appel on recupère l'équipement concerné puis que l'on execute les commandes pour recuperer les valeurs et mettre à jour celle-ci si necessaire.

Partie très importante : 
----
$cmd->setCollectDate('');
$cmd->event($value);
----

La première ligne est très importante car juste avant on à fait un execCmd qui va remplir le champs _collectDate (le _ devant le nom de l'attribut indique à Jeedom que l'attribut ne doit pas être sauvegardé en base, donc si vous en ajoutez pour votre class pensez bien à le preceder d'un _) or au moment de la fonction event (qui permet de signaler à Jeedom une nouvelle mise à jour de la valeur, avec declenchement de toute les actions qui doivent etre faites : mise à jour du dashboard, verification des scenario...), Jeedom regarde si la date de collecte est ancienne et si c'est le cas va refuser la nouvelle valeur. D'ou la remise à 0.

Ensuite on vide le cache (ici pas besoin de verifier s'il existe, l'objet est vide si le cache n'existe pas donc aucun risque pour la suppression) : 
----
$mc = cache::byKey('weatherWidgetmobile' . $weather->getId());
$mc->remove();
----

Vu que le cache est vide on force la generation des widget mobile et dashboard : 
----
$weather->toHtml('mobile');
$weather->toHtml('dashboard');
----

Enfin on previent jeedom que le widget est à rafraichir sur l'interface de l'utilisateur : 
----
$weather->refreshWidget();
----

Pour la classe commande, un petit truc à savoir si vous utilisez le template js de base. Lors de l'envoi de l'équipment Jeedom fait du differentiel sur les commandes et va supprimer celles qui sont en base mais pas dans la nouvelle definition de l'équipement. Voila comment l'éviter : 
----
 public function dontRemoveCmd() {
    return true;
}
----

Pour finir voici quelques trucs et astuces : 

- evitez (à moins de savoir ce que vous faites) d'écraser une methode de la classe heritée (ça peut causer pas mal de problèmes)
- Pour remonter la batterie (en %) d'un équipement, faite sur celui-ci (jeedom se chargera du reste et de prevenir l'utilisateur si nécessaire) : 
----
$eqLogic->batteryStatus(56);
----

- Sur les commandes au moment de l'ajout d'une valeur jeedom applique la methode d'instance formatValue($_value) qui en fonction du sous-type peut la remettre en forme (en particulier pour les valeurs binaires)
- ne faite JAMAIS une methode dans la class heritant de cmd s'appellant : execCmd ou event
- si dans la configuration de votre commande vous avez renseigné returnStateTime (en minute) et returnStateValue, jeedom changera automatique la valeur de votre commande par returnStateValue au bout de X minute(s)
- toujours sur la commande vous pouvez utiliser addHistoryValue pour forcer la mise en historique (attention votre commande doit etre historisée)
