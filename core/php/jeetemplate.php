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


/*
 * Point d'entrée du démon avec Jeedom 4, appelé avec la clé API du plugin.
 * Avec Jeedom 5, le démon envoie ses messages par l'API REST : ce fichier refuse alors tout.
 */
require_once __DIR__ . '/../../../../core/php/core.inc.php';

if (config::byKey('daemon::transport', 'template') == 'rest' || !jeedom::apiAccess(init('apikey'), 'template')) {
    http_response_code(403);
    die();
}
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo 'OK'; // test de connexion du démon au démarrage
    die();
}
$messages = json_decode(file_get_contents('php://input'), true);
if (is_array($messages)) {
    template::handleDaemonMessages($messages);
}
