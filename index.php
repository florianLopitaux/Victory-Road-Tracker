<?php
/**
 * @file     index.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  EntryPoint of the Victory-Road-Tracker, loads everything and does the 'url routing'.
 *
 * -------------------------------------------------------------------------
 *
 * Copyright (C) 2024 Victory-Road-Tracker
 * 
 * Use of this software is governed by the GNU Public License, version 3.
 *
 * Victory-Road-Tracker is free RESTFUL API: you can use it under the terms
 * of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Victory-Road-Tracker is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MindShare-API. If not, see <http://www.gnu.org/licenses/>.
 *
 * This banner notice must not be removed.
 *
 * -------------------------------------------------------------------------
 */

use apiService\{CharacterService, HissatsuService, StuffService};
use webController\{DatabaseController, HomeController};

// -------------------------------------------------------------------------

function loadAPIDependencies() : void {
    require_once 'api_/CharacterService.php';
    require_once 'api_/HissatsuService.php';
    require_once 'api_/StuffService.php';
}

function loadWebSiteDependencies() : void {
    require_once 'website/controller/DatabaseController.php';
    require_once 'website/controller/HomeController.php';
}

// -------------------------------------------------------------------------

function apiRouting(string $requestMethod, array $uri) : void {
    header('Content-Type: application/json; charset=utf-8');

    // check if api routing is specified
    if (strlen($uri[0]) === 0) {
        http_response_code(404);
        echo json_encode(array('response' => '404 Error ! No route specified in the url !'));
        die();
    }

    // Loads the .ini file that contains the database identifiers
    $config = parse_ini_file('config.ini');

    // file doesn't found or not parsable, should not happen
    if (!$config) {
        http_response_code(500);
        echo json_encode(array('response' => 'Internal problem'));
        die();
    }

    // get the authorization token to some restricted requests (requests that modify the database not just get)
    $headers = getallheaders();

    if (in_array('Authorization', $headers)) {
        $headerToken = $headers['Authorization'];
    } else if (in_array('HTTP_Authorization', $headers)) {
        $headerToken = $headers['HTTP_Authorization'];
    } else {
        $headerToken = '';
    }

    // parse the uri parameters to get the service to use
    $serviceName = strtoupper(substr($uri[0], 0, 1)) . substr($uri[0], 1);
    $serviceCalled = "apiService\\$serviceName" . 'Service';

    array_shift($uri);
    for ($i = 0; $i < count($uri); $i++) {
        $uri[$i] = urldecode($uri[$i]);
    }

    // check if the controller exists
    if (class_exists($serviceCalled)) {
        $service = new $serviceCalled($config, $requestMethod, $headerToken);
        $statusCode = $service->processRequest($uri, $_POST);

        http_response_code($statusCode);

    // controller doesn't find, bad uri routing
    } else {
        http_response_code(404);
        echo json_encode(array('response' => '404 Error ! The server doesn\'t found the route specified !'));
        die();
    }
}

function websiteRouting(string $requestMethod, array $uri) : void {
    // default controller if uri is empty
    if (strlen($uri[0]) === 0) {
        $controllerName = "Home";
    } else {
        // transform the first letter to upper to match with controllers name
        $controllerName = strtoupper(substr($uri[0], 0, 1)) . substr($uri[0], 1);
    }

    $controllerCalled = "webController\\$controllerName" . 'Controller';
    array_shift($uri);  // removed controller name in parameters array

    // check if the controller exists
    if (class_exists($controllerCalled)) {
        $controller = new $controllerCalled($requestMethod);

        if (count($uri) === 0 || strlen($uri[0]) === 0) {
            $statusCode = $controller->defaultAction();

        } else {
            $methodAction = $uri[0] . 'Action';
            array_shift($uri);  // removed action name method in parameters array

            if (method_exists($controller, $methodAction)) {
                $statusCode = $controller->$methodAction($uri);
            } else {  // controller doesn't find, bad uri routing
                $statusCode = 404;
            }
        }
    } else {  // controller doesn't find, bad uri routing
        $statusCode = 404;
    }

    http_response_code($statusCode);
    if ($statusCode === 404) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Victory Road Tracker</title>
        </head>
        <body>
            <h1>404 Error ! The server doesn\'t found the route specified !</h1>
        </body>
        </html>
        ';
    }
}

// -------------------------------------------------------------------------

// get the request method (GET, POST, PUT or DELETE)
$requestMethod = $_SERVER['REQUEST_METHOD'];

// parse the url to get the uri parameters and know which methods called
$uriParameters = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
array_shift($uriParameters); // remove first element always empty

// check if the request is for the API or the website
if (sizeof($uriParameters) > 0 && $uriParameters[0] === 'api') {
    loadAPIDependencies();
    array_shift($uriParameters); // remove 'api' element
    apiRouting($requestMethod, $uriParameters);

} else {
    loadWebSiteDependencies();
    websiteRouting($requestMethod, $uriParameters);
}
