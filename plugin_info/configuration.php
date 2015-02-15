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
            <label class="col-lg-4 control-label">{{KNX BAOS IP}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="knxipbaosAddr" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{KNX BAOS port}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="knxipbaosPort" value="80" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Modele}}</label>
            <div class="col-lg-2">
                <select class="configKey form-control" data-l1key="knxipbaosModel">
                    <option value="knxipbaos771">KNX IP BAOS 771</option>
                    <option value="knxipbaos772">KNX IP BAOS 772</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Synchronisation}}</label>
            <div class="col-lg-2">
              <a class="btn btn-default" id="bt_syncWithKnxipbaos"><i class="fa fa-retweet"></i> Synchroniser</a>
          </div>
      </div>
  </fieldset>
</form>

<script>
    $('#bt_syncWithKnxipbaos').on('click',function(){
 $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/knxipbaos/core/ajax/knxipbaos.ajax.php", // url du fichier php
            data: {
                action: "synchronisation",
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
            $('#div_alert').showAlert({message: '{{Synchronisation réussie}}', level: 'success'});
        }
    });
});
</script>

