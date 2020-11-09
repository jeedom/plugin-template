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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';


include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
			<label class="col-lg-4 control-label">{{Supprimer tous les fichiers Attestations}}</label>
			<div class="col-lg-5">
				<a class="btn btn-warning" id="bt_deleteAllCA_Btn"><i class="fas fa-sync-alt"></i> {{Effacer}}</a>
			</div>
		</div>
  </fieldset>
    <fieldset>
        <div class="form-group">
			<label class="col-lg-4 control-label">{{Fichier de Certificat}}</label>
            <div class="col-lg-2">
                <select class="configKey form-control eqLogicAttr" data-l1key="certificate_name">
                <?php
                    $path=realpath(dirname(__FILE__). '/../').'/3rdparty/Certificate';
                    if (!is_dir($path)){
                        log::add('CovidAttest', 'error', '[CONF] path :'.$path.' Not FOUND ');
                    }
                    $files = glob($path.'/*');
                    foreach($files as $file){ // iterate files
                        if(is_file($file) && preg_match('/\.pdf$/', basename ($file))){
                             
                          $fname = basename ($file);
                          log::add('CovidAttest', 'debug', ' pdf file found '.basename ($file));
                          echo '<option value="'.$fname.'">'.$fname."</option>";

                        }
                      }
                    
                ?>
                </select>
            </div>
            <div class="col-lg-5">
				<a class="btn btn-info" id="bt_modalUpload_CA"><i class="fas fa-download"></i> {{Upload new File}}</a>
				<a class="btn btn-warning" id="bt_modal_CA"><i class="fas jeedomapp-preset"></i> {{File Parameters}}</a>
                <a class="btn btn-info" id="bt_tests_CA"><i class="fas fa-eye"></i> {{Test Params}}</a>
                <a class="btn btn-info" id="bt_zip_CA"><i class="fas fa-eject"></i> {{share configuration}}</a>

			</div>
		</div>
  </fieldset>
</form>
<script>
$('#bt_modal_CA').on('click', function () {
        $('#md_modal').dialog({title: "Configuration pour "+$('.eqLogicAttr[data-l1key=certificate_name]').value()});
        $('#md_modal').load('index.php?v=d&plugin=CovidAttest&modal=modal.CovidAttest&cname=' + $('.eqLogicAttr[data-l1key=certificate_name]').value()).dialog('open');
});
$('#bt_modalUpload_CA').on('click', function () {
        $('#md_modal').dialog({title: "Chargement nouveau certificat"});
        $('#md_modal').load('index.php?v=d&plugin=CovidAttest&modal=modal.upload_ca').dialog('open').parent().width(750).height(200);
});
$('#bt_deleteAllCA_Btn').on('click', function () {
  	if (!confirm('{{Supprimer tous les fichiers attestations conservés de tous les équipement CovidAttest ?}}')) {
      	$('#div_alert').showAlert({message: '{{Opération annulée}}', level: 'success'});
        return false;
    }
	 $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/CovidAttest/core/ajax/CovidAttest.ajax.php", // url du fichier php
            data: {
                action: "delete_allFiles",
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alert').showAlert({message: '{{Réussie}}', level: 'success'});
            }
        });

       
});
$('#bt_tests_CA').on('click', function () {
    $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/CovidAttest/core/ajax/CovidAttest.ajax.php", // url du fichier php
            data: {
                action: "test_file",
                file:$('.eqLogicAttr[data-l1key=certificate_name]').value()
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                window.open(data.result); 
                
                $('#div_alert').showAlert({message: '{{Réussie}}', level: 'success'});
            }
        });
});

$('#bt_zip_CA').on('click', function () {  
    $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/CovidAttest/core/ajax/CovidAttest.ajax.php", // url du fichier php
            data: {
                action: "share_conf",
                file:$('.eqLogicAttr[data-l1key=certificate_name]').value()
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                window.open(data.result); 
                $('#div_alert').showAlert({message: '{{Réussie}}', level: 'success'});
            }
            
        });
        
});

</script>