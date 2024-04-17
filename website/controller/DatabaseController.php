<?php
/**
 * @file     website/controller/DatabaseController.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Controller of the Home page, manage the back-end of the page.
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

namespace webController;

use view\{DatabaseView, Layout};
require_once 'website/view/pages/DatabaseView.php';
require_once 'website/view/Layout.php';

class DatabaseController {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private string $requestMethod;
    private array $postParameters;

    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(string $requestMethod, array $postParameters = array()) {
        $this->requestMethod = $requestMethod;
        $this->postParameters = $postParameters;
    }


    // -------------------------------------------------------------------------
    // PUBLIC METHODS
    // -------------------------------------------------------------------------

    public function defaultAction(): int {
        $view = new DatabaseView(new Layout('website/view/html/layout.html'));
        $view->display();

        return 200;
    }
}
