<?php
/**
 * @file     api_/BaseService.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Base abstract class of api services.
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

abstract class BaseService {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    protected array $config;
    protected string $requestMethod;
    protected bool $has_token_auth;


    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(array $config, string $requestMethod, string $headerToken) {
        $this->config = $config;
        $this->requestMethod = $requestMethod;

        if ($headerToken == null) {
            $this->has_token_auth = false;
        } else {
            $this->has_token_auth = preg_match('/Bearer\s(\S+)/', $headerToken, $config['api_token']);
        }
    }


    // -------------------------------------------------------------------------
    // PUBLIC METHODS
    // -------------------------------------------------------------------------

    public function processRequest(array $uri, array $post): int {
        return 200;
    }
}
