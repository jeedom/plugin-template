
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

function displayEqLogic(_data) {
    
}

function getSideBarList() {
    $.ajax({
        type: "POST",
        url: "plugins/weather/core/ajax/weather.ajax.php", // ne pas oublier de modifier pour le nom de votre plugin
        data: {
            action: "sidebar"
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state !== 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#ul_eqLogicView').empty();
            $('#ul_eqLogicView').append(data.result);
            modifyWithoutSave = false;
        }
    });
}

function getContainer(_callback) {
    $.ajax({
        type: "POST",
        url: "plugins/weather/core/ajax/weather.ajax.php", // ne pas oublier de modifier pour le nom de votre plugin
        data: {
            action: "container"
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state !== 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('.eqLogicThumbnailContainer').empty();
            //$('.eqLogicThumbnailContainer').html(data.result);
            $('.eqLogicThumbnailDisplay legend').after($('<div class="eqLogicThumbnailContainer">').html(data.result));
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
        }
    });
}

