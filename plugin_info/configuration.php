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
            <label class="col-lg-4 control-label">{{Global param 1}}</label>
            <div class="col-lg-2">
                <input type="text" class="configKey form-control" data-l1key="param1" value="{{Default value}}"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Global param 2}}</label>
            <div class="col-lg-2">
                <input type="checkbox" class="configKey form-control" data-l1key="param2" checked="checked" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Global param 3}}</label>
            <div class="col-lg-2">
                <select class="configKey form-control" data-l1key="param3">
                    <option value="value1">{{First value}}</option>
                    <option value="value2">{{Second value}}</option>
                </select>
            </div>
        </div>
  </fieldset>
</form>

