// gestion bouton sauvegarde du json
$('#bt_save_conf_ca').on('click',function(){
  
  // vérification
  if($('form.form-ca-opt').find('.error_ca').length !== 0){
    $('#alerte_message_ca').showAlert({message: '{{Des données ne sont pas valides}}', level: 'danger'});
    return;
  }

  var form_data = $('form.form-ca-opt').serialize();
  console.log(form_data);

  $.ajax({// fonction permettant de faire de l'ajax
  type: "POST", // methode de transmission des données au fichier php
    url: "plugins/CovidAttest/core/ajax/CovidAttest.ajax.php", // url du fichier php
    data: {
        action: "save_json",
        data : form_data
    },
    dataType: 'json',
    error: function (request, status, error) {
        handleAjaxError(request, status, error);
        $('#alerte_message_ca').showAlert({message: data.result, level: 'danger'});
    },
    success: function (data) { // si l'appel a bien fonctionné
        if (data.state != 'ok') {
            $('#alerte_message_ca').showAlert({message: data.result, level: 'danger'});
            return;
        }
        $('#div_alert').showAlert({message: '{{Réussie}}', level: 'success'});
        $('#md_modal').dialog('close');
    }
  });


})



/// gestion de la validité des input
$('.input_ca_conf').on('focusout',function(){
  var rg = new RegExp('^[0-9\.]{1,8}$');
  console.log("TEST :"+this.value+" | "+rg.test(this.value));
  if(rg.test(this.value)){
    $(this).parent().removeClass('error_ca');
    console.log('entrée valide');
  }else{
    $(this).parent().addClass('error_ca');
    console.log('entrée NON valide');
  }
})