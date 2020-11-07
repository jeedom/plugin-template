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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

?>
<style>
#userfile, #jsonfile{
    display:none;
}
.arrowCA {
  border: solid var(--txt-color);
  border-width: 0 3px 3px 0;
  display: inline-block;
  padding: 3px;

}
.down {
  transform: rotate(45deg);
  -webkit-transform: rotate(45deg);
}
.right {
  transform: rotate(-45deg);
  -webkit-transform: rotate(-45deg);
}
.toggle-ca{
    margin-left:12px;
    cursor: pointer !important;
}
.btnGPE{
    width:100%;
    display: flex;
    align-items: end;
    justify-content: end;

}
</style>

<!-- Le type d'encodage des données, enctype, DOIT être spécifié comme ce qui suit -->
<form enctype="multipart/form-data" action="_URL_" method="post">
<div class="form-group" id="uploadGroupPDF">
  <!-- MAX_FILE_SIZE doit précéder le champ input de type file -->
  <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
  <!-- Le nom de l'élément input détermine le nom dans le tableau $_FILES -->
 
    <label  for="userfile"class="btn btn-info">
        <i class="fas fa-file"></i> {{Fichier de certificat PDF}}</a>
    </label>
    <input id="userfile" type="file" />
    <input id="fileName" type="text" size="50" readonly/>
 <!--<input  class="btn btn-info" id="userfile" type="file" /> -->

  
</div>
<div class="form-group"><i class="arrowCA down" id="arrowCA"></i><span class="toggle-ca"> {{ajouter un fichier de configuration}}</span></div>
<div class="form-group" id="uploadGroupJSON" style="display:none">
  <!-- MAX_FILE_SIZE doit précéder le champ input de type file -->

    <label  for="jsonfile" class="btn btn-info">
        <i class="fas fa-file"></i> {{Fichier de configuration JSON}}</a>
    </label>
    <input id="jsonfile" type="file" />
    <input id="jsonFileName" type="text" size="50" readonly/>
 <!--<input  class="btn btn-info" id="userfile" type="file" /> -->

  
</div>
<div class="form-group btnGPE" id="" >
<a class="btn btn-warning" id="bt_launch_upload_CA"><i class="fas fa-download"></i> {{Upload}}</a>
</div>
</form>
      <div id='alerte_message_ca' class="jqAlert alert-danger" style="width: 100%;display:none;">no alerte</div>
<script>
$('#userfile').on('change', function () {
    $('#fileName').val(this.files[0].name);
});
$('#jsonfile').on('change', function () {
    $('#jsonFileName').val(this.files[0].name);
});


$('.toggle-ca').on('click', function () {
    if($('#uploadGroupJSON').is(":visible")){
        $('#uploadGroupJSON').hide(200);
        $('#arrowCA').addClass('down');
        $('#arrowCA').removeClass('right');
        $('#jsonfile').val('');
        $('#jsonFileName').val('');

    }else{
        $('#arrowCA').addClass('right');
        $('#arrowCA').removeClass('down');
        $('#uploadGroupJSON').show(200);
    }
    
});

$('#bt_launch_upload_CA').on('click', function () {

    var file_data = $("#userfile").prop("files")[0];
    var jsonfile_data = $("#jsonfile").prop("files")[0];

    if(typeof file_data == "undefined"){
      	$('#alerte_message_ca').showAlert({message: 'No file selected', level: 'danger'});
        return;

    } 
    var form_data = new FormData();
    form_data.append("file", file_data);
    if(typeof jsonfile_data != "undefined"){
        form_data.append("json_file", jsonfile_data);
    }
    
    $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/CovidAttest/core/ajax/AttestUpload.ajax.php", // url du fichier php
            data: form_data,
            contentType: false,
            cache: false,
            processData:false,
            dataType: 'json',

            error: function (request, status, error) {
                handleAjaxError(request, status, error);
                $('#alerte_message_ca').showAlert({message: error, level: 'danger'});
                //$('#md_modal').dialog('close');
                return;
            },

            success: function (data) { // si l'appel a bien fonctionné
                if (data.result != 'ok') {
                    $('#alerte_message_ca').showAlert({message: 'error :'+data.result, level: 'danger'});
                }else{
                    $('#div_alert').showAlert({message: '{{Réussie, la page vas se recharger}}', level: 'success'});
                   $('#md_modal').dialog('close');
                  location.reload();
                }
                
               
            }
        });

        
});
</script>