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

function weather_update() {
    foreach (eqLogic::byType('weather') as $weather) {
        foreach ($weather->getCmd() as $cmd) {
            if ($cmd->getName() == 'Température') {
                $cmd->setLogicalId('temperature');
            }
            if ($cmd->getName() == 'Humidité') {
                $cmd->setLogicalId('humidity');
            }
            if ($cmd->getName() == 'Pression') {
                $cmd->setLogicalId('pressure');
            }
            if ($cmd->getName() == 'Condition') {
                $cmd->setLogicalId('condition');
            }
            if ($cmd->getName() == 'Condition Actuelle') {
                $cmd->setLogicalId('condition_now');
            }
            if ($cmd->getName() == 'Vitesse du vent') {
                $cmd->setLogicalId('wind_speed');
            }
            if ($cmd->getName() == 'Direction du vent') {
                $cmd->setLogicalId('wind_direction');
            }
            if ($cmd->getName() == 'Lever du soleil') {
                $cmd->setLogicalId('sunrise');
            }
            if ($cmd->getName() == 'Coucher du soleil') {
                $cmd->setLogicalId('sunset');
            }
            if ($cmd->getName() == 'Température Min') {
                $cmd->setLogicalId('temperature_min');
            }
            if ($cmd->getName() == 'Température Max') {
                $cmd->setLogicalId('temperature_max');
            }
            if ($cmd->getName() == 'Température Min +1') {
                $cmd->setLogicalId('temperature_1_min');
            }
            if ($cmd->getName() == 'Température Max +1') {
                $cmd->setLogicalId('temperature_1_max');
            }
            if ($cmd->getName() == 'Condition +1') {
                $cmd->setLogicalId('condition_1');
            }
            $cmd->save();
        }
        $weather->save();
    }
}

?>
