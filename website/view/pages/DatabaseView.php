<?php
/**
 * @file     website/view/pages/DatabaseView.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class to build the html of the Database page.
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

namespace view;

require_once 'website/view/BaseView.php';


final class DatabaseView extends BaseView {
    public function __construct(Layout $layout) {
        parent::__construct($layout);

        // set the title
        $this->title = 'Database';

        // set css files for this page
        $this->styles[] = 'pages/database';

        // set the html content
        $this->content = '<main>
            
        </main>';
    }
}