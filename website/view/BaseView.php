<?php
/**
 * @file     website/view/BaseView.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Abstract base class for all pages of the website.
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

require_once 'website/view/Layout.php';


abstract class BaseView {
    // FIELDS
    private Layout $layout;

    protected string $title;
    protected array $styles;
    protected string $content;


    // CONSTRUCTOR
    /**
     * The constructor of the BaseView abstract class.
     *
     * @param Layout $layout The Layout object of web views.
     */
    public function __construct(Layout $layout) {
        $this->layout = $layout;

        // set default css present in all pages
        $this->styles = array('global', 'header', 'footer');
    }


    // METHODS
    /**
     * This method is used to display the html content of the web page.
     * It called the display method of the layout object with parameters
     *
     * @return void
     */
    public function display(): void {
        // append the header and footer in the page
        $header = file_get_contents('/view/html/header.html');
        $this->content = $header . $this->content;

        $footer = file_get_contents('/view/html/footer.html');
        $this->content = $this->content . $footer;

        // create all link elements for css files
        $stylesHTML = '';

        foreach ($this->styles as $currentStyleFile) {
            $stylesHTML .= '<link rel="stylesheet" href="/assets/css/' . $currentStyleFile . '.css">';
        }

        // display the html page
        $this->layout->display("VictoryRoad Tracker - $this->title", $stylesHTML, $this->content);
    }
}
