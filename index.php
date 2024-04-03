<?php
/**
@file     index.php
@author   Florian Lopitaux
@version  0.1
@summary  EntryPoint of the Victory-Road-Tracker, loads everything and does the 'url routing'.

-------------------------------------------------------------------------

Copyright (C) 2024 Victory-Road-Tracker

Use of this software is governed by the GNU Public License, version 3.

Victory-Road-Tracker is free RESTFUL API: you can use it under the terms
of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Victory-Road-Tracker is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with MindShare-API. If not, see <http://www.gnu.org/licenses/>.

This banner notice must not be removed.

-------------------------------------------------------------------------
 */

// parse the url to get the uri parameters and know which methods called
$uriParameters = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
array_shift($uriParameters); // remove first element always empty

// check if the request is for the API or the website
if (sizeof($uriParameters) > 0 && $uriParameters[0] === 'api') {
    header('Content-Type: application/json');

    // Loads the .ini file that contains the database identifiers
    $config = parse_ini_file('config.ini');

    // file doesn't found or not parsable, should not happen
    if (!$config) {
        http_response_code(500);
        echo json_encode(array('response' => 'Internal problem'));
        die();
    }
}

// the request is for the website
if (sizeof($uriParameters) === 0) {
    $uriParameters[] = "Home";
} else {
    // transform the first letter to upper to match with controllers name
    $uriParameters[0] = strtoupper(substr($uriParameters[0], 0, 1)) . substr($uriParameters[0], 1);
}

