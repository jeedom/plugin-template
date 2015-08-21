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
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    // action qui permet d'obtenir l'ensemble des eqLogic
    if (init('action') == 'getAll') {
        $eqLogics = eqLogic::byType('template'); // ne pas oublier de modifier pour le nom de votre plugin
        // la liste des équipements
        foreach ($eqLogics as $eqLogic) {
            $data['id'] = $eqLogic->getId();
            $data['humanSidebar'] = $eqLogic->getHumanName(true, false);
            $data['humanContainer'] = $eqLogic->getHumanName(true, true);
            $return[] = $data;
        }
        ajax::success($return);
    }
    // action qui permet d'effectuer la sauvegarde des donéée en asynchrone
    if (init('action') == 'saveStack') {
        $params = init('params');
        ajax::success(template::saveStack($params)); // ne pas oublier de modifier pour le nom de votre plugin
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
3