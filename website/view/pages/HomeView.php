<?php
/**
 * @file     website/view/pages/HomeView.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class to build the html of the Home page.
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


final class HomeView extends BaseView {
    public function __construct(Layout $layout) {
        parent::__construct($layout);

        // set the title
        $this->title = 'Home';

        // set css files for this page
        $this->styles[] = 'pages/home';

        // set the html content
        $this->content = '<main>
            <h1 class="font-black">Bienvenue sur Inazuma Eleven Victory Road Tracker !</h1>
            <span class="font-bold">
                <img id="left" class="lightning" src="assets/img/lightning.png" alt="éclair pour décorer le texte">
                Choisissez votre outil
                <img id="right" class="lightning" src="assets/img/lightning.png" alt="éclair pour décorer le texte">
            </span>
            
            <section id="menu-buttons">
                <a href="/teambuilder">Team Builder</a>
                <a href="/dle">DLE</a>
                <a href="/database">Base de données</a>
                <a href="/articles">Articles</a>
            </section>

            <!-- script for pages under developpement -->
            <script type="application/javascript">
                let menu = document.getElementById("menu-buttons");

                Array.from(menu.children).forEach(child => {
                    if (child.tagName === "A") {
                        child.onclick = (event) => {
                            event.preventDefault();
                            alert("Cette page est en cours de développement.");
                        }
                    }
                });
            </script>
        </main>';
    }
}
