
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



/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */

/* Fonction appelé pour mettre l'affichage du tableau des commandes de votre eqLogic
 * _cmd: les détails de votre commande
 */
/* global jeedom */

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}"></td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

/* Fonction appelé pour mettre l'affichage à jour pour la sauvegarde en temps réel
 * _data: les détails des informations à sauvegardé
 */
function displayEqLogic(_data) {
    
}

/* Fonction appelé pour mettre l'affichage à jour de la sidebar et du container 
 * en asynchrone, est appelé en début d'affichage de page, au moment de la sauvegarde,
 * de la suppression, de la création
 * _callback: obligatoire, permet d'appeler une fonction en fin de traitement
 */
function updateDisplayPlugin(_callback) {
    $.ajax({
        type: "POST",
        url: "plugins/template/core/ajax/template.ajax.php", // ne pas oublier de modifier pour le nom de votre plugin
        data: {
            action: "getAll"
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            //console.log(data);
            if (data.state !== 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var htmlSideBar = '';
            var htmlContainer = '';
            // Le plus Geant - ne pas supprimer
            htmlContainer += '<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
            htmlContainer += '<center>';
            htmlContainer += '<i class="fa fa-plus-circle" style="font-size : 7em;color:#94ca02;"></i>';
            htmlContainer += '</center>';
            htmlContainer += '<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>Ajouter</center></span>';
            htmlContainer += '</div>';
            // la liste des équipements
            var eqLogics = data.result;
            for (var i  in eqLogics) {
                htmlSideBar += '<li class="cursor li_eqLogic" data-eqLogic_id="' + eqLogics[i].id + '"><a>' + eqLogics[i].humanSidebar + '</a></li>';
                // Définition du format des icones de la page principale - ne pas modifier
                htmlContainer += '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' + eqLogics[i].id + '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                htmlContainer += "<center>";
                // lien vers l'image de votre icone
                htmlContainer += '<img src="plugins/weather/doc/images/weather_icon.png" height="105" width="95" />';
                htmlContainer += "</center>";
                // Nom de votre équipement au format human
                htmlContainer += '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' + eqLogics[i].humanContainer + '</center></span>';
                htmlContainer += '</div>';
            }
            $('#ul_eqLogicView').empty();
            $('#ul_eqLogicView').append(htmlSideBar);
            $('.eqLogicThumbnailContainer').remove();
            $('.eqLogicThumbnailDisplay legend').after($('<div class="eqLogicThumbnailContainer">').html(htmlContainer));
            $('.eqLogicThumbnailContainer').packery();
            $("img.lazy").lazyload({
                container: $(".eqLogicThumbnailContainer"),
                event : "sporty",
                skip_invisible : false
            });
            $("img.lazy").trigger("sporty");
            $("img.lazy").each(function () {
                var el = $(this);
                if (el.attr('data-original2') !== undefined) {
                    $("<img>", {
                        src: el.attr('data-original'),
                        error: function () {
                            $("<img>", {
                                src: el.attr('data-original2'),
                                error: function () {
                                    if (el.attr('data-original3') !== undefined) {
                                        $("<img>", {
                                            src: el.attr('data-original3'),
                                            error: function () {
                                                el.lazyload({
                                                    event: "sporty"
                                                });
                                                el.trigger("sporty");
                                            },
                                            load: function () {
                                                el.attr("data-original", el.attr('data-original3'));
                                                el.lazyload({
                                                    event: "sporty"
                                                });
                                                el.trigger("sporty");
                                            }
                                        });
                                    } else {
                                        el.lazyload({
                                            event: "sporty"
                                        });
                                        el.trigger("sporty");
                                    }
                                },
                                load: function () {
                                    el.attr("data-original", el.attr('data-original2'));
                                    el.lazyload({
                                        event: "sporty"
                                    });
                                    el.trigger("sporty");
                                }
                            });
                        },
                        load: function () {
                            el.lazyload({
                                event: "sporty"
                            });
                            el.trigger("sporty");
                        }
                    });
                } else {
                    el.lazyload({
                        event: "sporty"
                    });
                    el.trigger("sporty");
                }
            });
            if(_callback !== undefined)
                _callback();
            modifyWithoutSave = false;
        }
    });
}
