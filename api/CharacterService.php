<?php

namespace apiService;
require_once 'api/BaseService.php';

use data\CharacterAccess;
require_once 'data/CharacterAccess.php';

use model\{Character, Element};
require_once 'model/Character.php';
require_once 'model/Element.php';


class CharacterService extends BaseService {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private CharacterAccess $dbCharacter;

    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(array $config, string $requestMethod, string $headerToken) {
        parent::__construct($config, $requestMethod, $headerToken);
        $dbCharacter = new CharacterAccess($config['db_identifier'], $config['db_identifier']);
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
            $response['content'] = 'http request method not allowed for "/character"';
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
            $response['content'] = 'Bad request ! The "/character" DELETE method has to have one argument !';
        }

        $has_remove = $this->dbCharacter->deleteCharacter($uri[0]);

        if ($has_remove) {
            $response['code'] = 200;
            $response['content'] = $uri[0] . ' has been deleted.'
        } else {
            $response['code'] = 422;
            $response['content'] = $uri[0] . ' doesn\'t found in the database, can\'t delete';
        }

        return $response;
    }

    // -------------------------------------------------------------------------

    private function processPost(array $uri, array $post): array {
        $response = array();
        $character = null;

        try {
            $character = Character::fromArray($post);
        } catch (Exception $e) {
            $response['code'] = 422;
            $response['content'] = 'Impossible to transform the body POST request in Character model.';
        }

        if ($character != null) {
            $this->dbCharacter->insertCharacter($character);
            $response['code'] = 200;
            $response['content'] = 'Character entity correctly inserted.'
        } 

        return $response;
    }

    // -------------------------------------------------------------------------

    private function processGet(array $uri, array $post) {
        $response = array();

        if (sizeof($uri) === 0) {
            $characters = $this->dbCharacter->getAllCharacters();

            $response['code'] = 200;
            $response['content'] = $this->transformToArray($characters);

        } else if (sizeof($uri) === 1) {
            $character = $this->dbCharacter->getCharacter($uri[0]);

            if ($character == null) {
                $response['code'] = 422;
                $response['content'] = $uri[0] . ' doesn\'t found in the database.'
            } else {
                $response['code'] = 200;
                $response['content'] = $character->toArray();
            }

        } else if (sizeof($uri) === 2 && $uri[0] === 'element') {
            $element = Element::fromString($uri[1]);

            if ($element == null) {
                $response['code'] = 422;
                $response['content'] = $uri[1] . ' isn\'t a correct element.';
            } else {
                $characters = $this->dbCharacter->getElementCharacters($element);

                $response['code'] = 200;
                $response['content'] = $this->transformToArray($characters);
            }

        } else {
            $response['code'] = 400;
            $response['content'] = 'Bad request routing !'
        }

        return $response;
    }

    // -------------------------------------------------------------------------

    private function transformToArray(array $characters): array {
        $responseContent = array();

        foreach ($characters as $current) {
            $responseContent[] = $current->toArray();
        }

        return $responseContent;
    }
}
