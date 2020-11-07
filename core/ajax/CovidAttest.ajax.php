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
    //require_once dirname(__FILE__) . '/../class/AttestUpload.class.php';
    require_once dirname(__FILE__) . '/../class/AttestJSON.class.php';
    require_once dirname(__FILE__) . '/../class/AttestGen.class.php';
    require_once dirname(__FILE__) . '/../class/CovidAttest.class.php';
    include_file('core', 'authentification', 'php');
    
    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    ajax::init();
    
    log::add('CovidAttest','debug','action required :'.init('action'));


    if (init('action') == 'delete_allFiles') {
      CovidAttest::DELETE_ALL();
      ajax::success();
    }
    if (init('action') == 'upload_files') { 
      //$img = $_FILES['file']['name'];
      //$tmp = $_FILES['file']['tmp_name'];
      log::add('CovidAttest','debug','pdf file :'.init('files'));
      //AttestUpload::UPLOAD_FILE(init('file'));
      ajax::success();
    }
    
    if (init('action') == 'save_json') { 
      ATTESTJSON::save_json(init('data'));
      ajax::success();
    }
    if (init('action') == 'test_file') { 
      //send file
      log::add('CovidAttest', 'debug','call test file :'.init('file'));
      $filename=CovidAttest::generate_test(init('file'));
      ajax::success($filename);
    }
    if (init('action') == 'share_conf') { 
      //send file
      log::add('CovidAttest', 'debug','call share file :'.init('file'));
      $filename=ATTESTJSON::share_conf_file(init('file'));
      if($filename!=false){
        ajax::success($filename);
      }else{
        ajax::error("impossible de créer le zip", 666666);
      }
      
    }
  /* Fonction permettant l'envoi de l'entête 'Content-Type: application/json'
    En V3 : indiquer l'argument 'true' pour contrôler le token d'accès Jeedom
    En V4 : autoriser l'exécution d'une méthode 'action' en GET en indiquant le(s) nom(s) de(s) action(s) dans un tableau en argument
  */  
    



    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}