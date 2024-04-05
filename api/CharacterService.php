<?php

namespace apiService;

require_once 'api/BaseService.php';

class CharacterService extends BaseService {

    // -------------------------------------------------------------------------
    // OVERRIDE METHODS
    // -------------------------------------------------------------------------

    public function processRequest(array $uri, array $post): int {
        switch ($this->requestMethod) {
            case 'GET':
                break;

            case 'POST':
                break;

            case 'DELETE':
                break;

            default:
                echo json_encode(array('response' => 'http request method not allowed for "/character"'), JSON_PRETTY_PRINT);
                return 405;
        }
    }
}
