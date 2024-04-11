<?php
/**
 * @file     api_/HissatsuService.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class to manage api calls beginning by the following route : /hissatsu.
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
require_once 'api_/BaseService.php';

use data\{CharacterAccess, HissatsuAccess};
require_once 'data/CharacterAccess.php';
require_once 'data/HissatsuAccess.php';

use model\{Element, Hissatsu, HissatsuType};
require_once 'model/Element.php';
require_once 'model/Hissatsu.php';
require_once 'model/HissatsuType.php';


class HissatsuService extends BaseService {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private HissatsuAccess $dbHissatsu;

    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(array $config, string $requestMethod, string $headerToken) {
        parent::__construct($config, $requestMethod, $headerToken);
        $this->dbHissatsu = new HissatsuAccess($config['db_identifier'], $config['db_password']);
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
            $response['content'] = 'http request method not allowed for "/hissatsu"';
        }

        echo json_encode(array('response' => $response['content']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $response['code'];
    }

    // -------------------------------------------------------------------------
    // PRIVATE METHODS
    // -------------------------------------------------------------------------

    private function processDelete(array $uri, array $post): array {
        $response = array();

        if (sizeof($uri) !== 1) {
            $response['code'] = 400;
            $response['content'] = 'Bad request ! The "/hissatsu" DELETE method has to have one argument !';
            return $response;
        }

        $has_remove = $this->dbHissatsu->deleteHissatsu($uri[0]);

        if ($has_remove) {
            $response['code'] = 200;
            $response['content'] = $uri[0] . ' has been deleted.';
        } else {
            $response['code'] = 422;
            $response['content'] = $uri[0] . ' doesn\'t found in the database, can\'t delete';
        }

        return $response;
    }

    // -------------------------------------------------------------------------

    private function processPost(array $uri, array $post): array {
        $response = array();
        $hissatsu = null;

        try {
            $hissatsu = Hissatsu::fromArray($post);
        } catch (\Exception $e) {
            $response['code'] = 422;
            $response['content'] = 'Impossible to transform the body POST request in Hissatsu model.';
        }

        if ($hissatsu != null) {
            $this->dbHissatsu->insertHissatsu($hissatsu);
            $response['code'] = 200;
            $response['content'] = 'Hissatsu entity correctly inserted.';
        }

        return $response;
    }

    // -------------------------------------------------------------------------

    private function processGet(array $uri, array $post): array {
        $response = array();

        if (sizeof($uri) === 0) {
            $hissatsu = $this->dbHissatsu->getAllHissatsu();

            $response['code'] = 200;
            $response['content'] = $this->transformToArray($hissatsu);

        } else if (sizeof($uri) === 1) {
            $hissatsu = $this->dbHissatsu->getHissatsu($uri[0]);

            if ($hissatsu == null) {
                $response['code'] = 422;
                $response['content'] = $uri[0] . ' doesn\'t found in the database.';
            } else {
                $response['code'] = 200;
                $response['content'] = $hissatsu->toArray();
            }

        } else if (sizeof($uri) === 2) {
            if ($uri[0] === 'element') {
                $element = Element::fromString($uri[1]);

                if ($element == null) {
                    $response['code'] = 422;
                    $response['content'] = $uri[1] . ' isn\'t a correct element.';
                } else {
                    $hissatsu = $this->dbHissatsu->getElementHissatsu($element);

                    $response['code'] = 200;
                    $response['content'] = $this->transformToArray($hissatsu);
                }
            } else if ($uri[0] === 'type') {
                $type = HissatsuType::fromString($uri[1]);

                if ($type == null) {
                    $response['code'] = 422;
                    $response['content'] = $uri[1] . ' isn\'t a correct Hissatsu type.';
                } else {
                    $hissatsu = $this->dbHissatsu->getTypeHissatsu($type);

                    $response['code'] = 200;
                    $response['content'] = $this->transformToArray($hissatsu);
                }
            } else if ($uri[0] === 'characters') {
                $characterAccess = new CharacterAccess($this->config['db_identifier'], $this->config['db_identifier']);
                $characters = $this->dbHissatsu->getHissatsuOwners($characterAccess, $uri[1]);

                $response['code'] = 200;
                $response['content'] = $this->transformToArray($characters);
            } else {
                $response['code'] = 400;
                $response['content'] = 'Bad request routing !';
            }

        } else {
            $response['code'] = 400;
            $response['content'] = 'Bad request routing !';
        }

        return $response;
    }

    // -------------------------------------------------------------------------

    private function transformToArray(array $tab): array {
        $responseContent = array();

        foreach ($tab as $current) {
            $responseContent[] = $current->toArray();
        }

        return $responseContent;
    }
}
