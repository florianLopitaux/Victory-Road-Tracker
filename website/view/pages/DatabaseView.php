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
            <a id="back-link" href="/home">
                <img src="assets/img/back_arrow.png" alt="Flèche en arrière pour revenir à la page précédente.">
            </a>

            <section id="menu-section">
                <button id="selected" class="left-btn">Personnages</button>
                <button class="middle-btn">Techniques</button>
                <button class="right-btn">Objets</button>
            </section>

            <table id="players">
            <thead>
            <tr>
                <th scope="col" rowspan="2">Name</th>
                <th scope="col" rowspan="2">Élément</th>
                <th scope="col" colspan="7">Statistiques</th>
                <th scope="col" rowspan="2" class="sortable" onclick="sortTable(8)">Total <span class="arrow">=</span> </th>
            </tr>
            <tr>
                <th scope="col" class="sortable" onclick="sortTable(1)">Frappe (ATT) <span class="arrow">=</span> </th>
                <th scope="col" class="sortable" onclick="sortTable(2)">Contrôle (ATT/MIL) <span class="arrow">=</span> </th>
                <th scope="col" class="sortable" onclick="sortTable(3)">Pression (DEF) <span class="arrow">=</span> </th>
                <th scope="col" class="sortable" onclick="sortTable(4)">Physique (GAR/DEF) <span class="arrow">=</span> </th>
                <th scope="col" class="sortable" onclick="sortTable(5)">Agilité (GAR) <span class="arrow">=</span> </th>
                <th scope="col" class="sortable" onclick="sortTable(6)">Intelligence (DEF/MIL) <span class="arrow">=</span> </th>
                <th scope="col" class="sortable" onclick="sortTable(7)">Technique (MIL) <span class="arrow">=</span> </th>
            </tr>
            </thead>

            <tbody>';

        // TODO : foreach data in parameter of the constructor create a row with stats
        // example of character row
        $this->content = $this->content . '
                <tr>
                    <th scope="row"><a href="mark_evans.html">Mark Evans</a></th>
                    <td>Terre</td>
                    <td>50</td>
                    <td>50</td>
                    <td>50</td>
                    <td>50</td>
                    <td>50</td>
                    <td>50</td>
                    <td>50</td>
                    <td>350</td>
                </tr>
        ';

        $this->content = $this->content . '
            </tbody>
        </main>';
    }
}
