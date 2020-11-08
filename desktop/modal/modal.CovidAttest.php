<style>
.label_ca-blue{
    color: var(--sc-lightTxt-color) !important;
    background-color: var(--al-info-color) !important;
    width:75px;
}
.label_ca-yellow{
    color: var(--sc-lightTxt-color) !important;
    background-color: var(--al-warning-color) !important;
    width:40px;
}
.label_ca {
    
    font-size: 12px;
    border: none;
    padding: 7px 8px;
    height: 32px;
}
.CA_wrap {
    display: inline !important;
    padding: 7px 8px;
    height: 32px;
}
.CA_wrap.error_ca {
    border: solid red !important;
}
</style>

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
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/../../../../core/php/utils.inc.php';
require_once __DIR__. '/../../core/class/AttestJSON.class.php';

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$fName=$_GET['cname']; 
$arrPart = ATTESTJSON::extract_json_from_filename($fName);
log::add("CovidAttest", 'debug', '╠════ json loaded : ');


echo '<div id="" class="col-xs-12 eqLogic">';
    echo '<form class="form-ca-opt">';
        echo '<div class="form-group">';
            echo '<label  class="label_ca label_ca-yellow" style="width:100px!important;">{{Fichier}} :</label>';
            echo '<input id="fileName" type="text" size="50" value="'.$fName.'"readonly name="filename"/>';
// la divp pour les alertes
            echo '<div id="alerte_message_ca" class="jqAlert alert-danger" style="width: 100%;display:none;">no alerte</div>';
// les boutons        
        echo '</div>';
        echo '<a class="btn btn-danger" id="bt_save_conf_ca"><i class="fas fa-download"></i>{{Save}}</a>';
/// le formulaire

foreach($arrPart as $k => $v){
    
        echo '<div class="form-group">';
            echo '<label  class="label_ca label_ca-blue">'.$k.'</label>';
     
     //log::add("CovidAttest", 'debug', 'parse to print :'.$k.' | '.is_array($v));
     if (is_array($v)){
        foreach($v as $kk => $vv){
            echo '<div class="CA_wrap"><label class="label_ca label_ca-yellow" >'.$kk.'</label>';
                echo '<input name="'.$k.'_'.$kk.'" type="text" value="'.$vv.'" size="5" class="label_ca input_ca_conf" />';
            echo '</div>';
        }

     }else{
            echo '<input name="'.$k.'" type="text" value="'.$v.'"size="5" />';
    }
        echo '</div>';
}

    echo '</form>';
echo '</div>';
include_file('desktop', 'CovidAttest_conf', 'js', 'CovidAttest');
?>