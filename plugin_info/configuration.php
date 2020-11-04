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
				<a class="btn btn-warning" id="bt_syncconfigBlea"><i class="fas fa-sync-alt"></i> {{Effacer}}</a>
			</div>
		</div>
  </fieldset>
</form>

<script>
   $('#bt_syncconfigBlea').on('click', function () {
  	if (!confirm('{{Supprimer tous les fichiers attestations conservés de tous les équipement CovidAttest ?}}')) {
      	$('#div_alert').showAlert({message: '{{Opération annulée}}', level: 'success'});
        return false;
    }
	 $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/CovidAttest/core/ajax/CovidAttest.ajax.php", // url du fichier php
            data: {
                action: "delete_allFiles",
				level : $(this).attr('data-log')
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

</script>