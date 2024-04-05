<?php
/**
 * @file     api/StuffService.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class to manage api calls beginning by the following route : /stuff.
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

namespace apiService;
require_once 'api/BaseService.php';

use data\StuffAccess;
require_once 'data/StuffAccess.php';

use model\{Stuff, StuffCategory};
require_once 'model/Stuff.php';
require_once 'model/StuffCategory.php';


class StuffService extends BaseService {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private StuffAccess $dbStuff;

    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(array $config, string $requestMethod, string $headerToken) {
        parent::__construct($config, $requestMethod, $headerToken);
        $dbStuff = new StuffAccess($config['db_identifier'], $config['db_identifier']);
    }

    // -------------------------------------------------------------------------
    // OVERRIDE METHODS
    // -------------------------------------------------------------------------

    public function processRequest(array $uri, array $post): int {
        $response = array();

        // check the bearer authorization token for some request methods
        if ($this->requestMethod === 'POST' || $this->requestMethod === 'DELETE') {
            echo json_encode(array('response' => 'This request needs the bearer authorization token !'), JSON_PRETTY_PRINT);
            return 401;
        }

        // get the method name to called
        $methodCalled = substr($this->requestMethod, 0, 1) . substr(strtolower($this->requestMethod), 1);
        $methodCalled = 'process' . $methodCalled;

        // check if request method supported for this class
        if (method_exists($this, $methodCalled)) {
            $response = $this->$methodCalled($uri, $post);

        } else {
            $response['code'] = 405;
            $response['content'] = 'http request method not allowed for "/stuff"';
        }

        echo json_encode(array('response' => $response['content']), JSON_PRETTY_PRINT);
        return $response['code'];
    }

    // -------------------------------------------------------------------------
    // PRIVATE METHODS
    // -------------------------------------------------------------------------

    private function processDelete(array $uri, array $post): array {
        $response = array();

        if (sizeof($uri) !== 1) {
            $response['code'] = 400;
            $response['content'] = 'Bad request ! The "/stuff" DELETE method has to have one argument !';
        }

        $stuff = $this->dbStuff->getStuff($uri[0]);

        if ($stuff == null) {
            $response['code'] = 422;
            $response['content'] = $uri[0] . ' doesn\'t found in the database, can\'t delete';
        } else {
            $this->dbCharacter->deleteStuff($stuff);

            $response['code'] = 200;
            $response['content'] = $uri[0] . ' has been deleted.'
        }

        return $response;
    }

    // -------------------------------------------------------------------------

    private function processPost(array $uri, array $post): array {
        $response = array();
        $stuff = null;

        try {
            $stuff = Stuff::fromArray($post);
        } catch (Exception $e) {
            $response['code'] = 422;
            $response['content'] = 'Impossible to transform the body POST request in Stuff model.';
        }

        if ($stuff != null) {
            $this->dbStuff->insertStuff($stuff);
            $response['code'] = 200;
            $response['content'] = 'Stuff entity correctly inserted.'
        } 

        return $response;
    }

    // -------------------------------------------------------------------------

    private function processGet(array $uri, array $post) {
        $response = array();

        if (sizeof($uri) === 0) {
            $stuffs = $this->dbStuff->getAllStuffs();

            $response['code'] = 200;
            $response['content'] = $this->transformToArray($stuffs);

        } else if (sizeof($uri) === 1) {
            $stuff = $this->dbStuff->getStuff($uri[0]);

            if ($stuff == null) {
                $response['code'] = 422;
                $response['content'] = $uri[0] . ' doesn\'t found in the database.'
            } else {
                $response['code'] = 200;
                $response['content'] = $stuff->toArray(true);
            }

        } else if (sizeof($uri) === 2 && $uri[0] === 'category') {
            $category = StuffCategory::fromString($uri[1]);

            if ($category == null) {
                $response['code'] = 422;
                $response['content'] = $uri[1] . ' isn\'t a correct stuff category.';
            } else {
                $stuffs = $this->dbStuff->getCategoryStuffs($category);

                $response['code'] = 200;
                $response['content'] = $this->transformToArray($stuffs);
            }
        } else {
            $response['code'] = 400;
            $response['content'] = 'Bad request routing !'
        }

        return $response;
    }

    // -------------------------------------------------------------------------

    private function transformToArray(array $stuffs): array {
        $responseContent = array();

        foreach ($stuffs as $current) {
            $responseContent[] = $current->toArray(true);
        }

        return $responseContent;
    }
}
