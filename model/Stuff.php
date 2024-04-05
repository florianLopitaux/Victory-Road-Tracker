<?php
/**
 * @file     model/Stuff.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class that represents an item that can be attached to a character in the game.
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

namespace model;

require_once 'model/StuffCategory.php';

class Stuff {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private string $name;
    private StuffCategory $category;
    private Statistics $stats;


    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(string $name, StuffCategory $category, Statistics $stats) {
        $this->name = $name;
        $this->category = $category;
        $this->stats = $stats;
    }


    // -------------------------------------------------------------------------
    // GETTERS
    // -------------------------------------------------------------------------

    public function getName(): string {
        return $this->name;
    }

    public function getCategory(): StuffCategory  {
        return $this->category;
    }

    public function getStats(): Statistics {
        return $this->stats;
    }


    // -------------------------------------------------------------------------
    // SETTERS
    // -------------------------------------------------------------------------

    public function setStats(Statistics $stats): void {
        $this->stats = $stats;
    }
}
