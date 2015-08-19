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

/******************************* Includes *******************************/ 
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class template extends eqLogic {
    /******************************* Attributs *******************************/ 
    /* Ajouter ici toutes vos variables propre à votre classe */

    /***************************** Methode static ****************************/ 

    /*
    // Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function cron() {

    }
    */

    /*
    // Fonction exécutée automatiquement toutes les heures par Jeedom
    public static function cronHourly() {

    }
    */

    /*
    // Fonction exécutée automatiquement tous les jours par Jeedom
    public static function cronDayly() {

    }
    */
 
    /*************************** Methode d'instance **************************/ 
 
    /* Fonction permettant de récupérer une liste des équipements pour la Sidebar de Jeedom
     * Entrée: Aucune
     * Retour: Renvoie la liste des équipements 
    */
    public static function getSideBarList() {
        $eqLogics = eqLogic::byType('template');
        $return = '';
        // la liste des équipements
        foreach ($eqLogics as $eqLogic) {
            $return .= '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
        }
        return $return;
    }

    /* Fonction permettant de récupérer une liste des équipements pour le container de Jeedom
     * Entrée: Aucune
     * Retour: Renvoie la liste des équipements 
    */
    public static function getContainer() {
        $eqLogics = eqLogic::byType('template');
        $return = '';
        // Le plus Geant obligatoire - ne pas supprimer
        $return .= '<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
        $return .= '<center>';
        $return .= '<i class="fa fa-plus-circle" style="font-size : 7em;color:#94ca02;"></i>';
        $return .= '</center>';
        $return .= '<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>Ajouter</center></span>';
        $return .= '</div>';
        
        // la liste équipements
        foreach ($eqLogics as $eqLogic) {
            // Définition du format des icones de la page principale - ne pas modifier
            $return .= '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
            $return .= "<center>";
            // lien vers l'image de votre icone
            $return .= '<img src="plugins/weather/doc/images/template_icon.png" height="105" width="95" />';
            $return .= "</center>";
            // Nom de votre équipement au format human
            $return .= '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
            $return .= '</div>';
        }
        return $return;
    }

    /************************** Pile de mise à jour **************************/ 
    
    /* fonction permettant d'initialiser la pile 
     * plugin: le nom de votre plugin
     * action: l'action qui sera utilisé dans le fichier ajax du pulgin 
     * callback: fonction appelé coté client(JS) pour mettre à jour l'affichage 
     */ 
    public function initStackData() {
        nodejs::pushUpdate('template::initStackDataEqLogic', array('plugin' => 'template', 'action' => 'saveStack', 'callback' => 'displayEqLogic'));
    }
    
    /* fonnction permettant d'envoyer un nouvel équipement pour sauvegarde et affichage, 
     * les données sont envoyé au client(JS) pour être traité de manière asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function stackData($params) {
        if(is_object($params)) {
            $paramsArray = utils::o2a($params);
        }
        nodejs::pushUpdate('template::stackDataEqLogic', $paramsArray);
    }
    
    /* fonction appelé pour la sauvegarde asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function saveStack($params) {
        // inserer ici le traitement pour sauvegarde de vos données en asynchrone
        
    }

    /* fonction appelé avant le début de la séquence de sauvegarde */
    public function preSave() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion 
     * dans la base de données pour une mise à jour d'une entrée */
    public function preUpdate() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde après l'insertion 
     * dans la base de données pour une mise à jour d'une entrée */
    public function postUpdate() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion 
     * dans la base de données pour une nouvelle entrée */
    public function preInsert() {

    }

    /* fonction appelé pendant la séquence de sauvegarde après l'insertion 
     * dans la base de données pour une nouvelle entrée */
    public function postInsert() {
        
    }

    /* fonction appelé après la fin de la séquence de sauvegarde */
    public function postSave() {
        
    }

    /* fonction appelé avant l'effacement d'une entrée */
    public function preRemove() {
        
    }

    /* fonnction appelé après l'effacement d'une entrée */
    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class templateCmd extends cmd {
    /******************************* Attributs *******************************/ 
    /* Ajouter ici toutes vos variables propre à votre classe */

    /***************************** Methode static ****************************/ 

    /*************************** Methode d'instance **************************/ 

    /* Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
    public function dontRemoveCmd() {
        return true;
    }
    */

    public function execute($_options = array()) {
        
    }

    /***************************** Getteur/Setteur ***************************/ 

    
}

?>
