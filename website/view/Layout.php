<?php
/**
 * @file     view/Layout.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  class to build the html skeleton of all pages.
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


final class Layout {
    // FIELDS
    private string $pathTemplateFile;


    // CONSTRUCTOR
    /**
     * The constructor of the Layout class.
     *
     * @param string $pathTemplateFile The path of the html layout file.
     */
    public function __construct(string $pathTemplateFile) {
        $this->pathTemplateFile = $pathTemplateFile;
    }


    // METHODS
    /**
     * This method is used to display the html content of the web page.
     *
     * @param string $title The title of the page.
     * @param string $styles The link style files html tags of the page.
     * @param string $content The html body content of the page.
     *
     * @return void
     */
    public function display(string $title, string $styles, string $content): void {
        $page = file_get_contents($this->pathTemplateFile);
        $page = str_replace(['%title%', '%styles%', '%content%'], [$title, $styles, $content], $page);

        echo $page;
    }
}
