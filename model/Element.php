<?php
/**
@file     model.Element.php
@author   Florian Lopitaux
@version  0.1
@summary  Enumeration class that contains the elements of the characters and hissatsu techniques.

-------------------------------------------------------------------------

Copyright (C) 2024 Victory-Road-Tracker

Use of this software is governed by the GNU Public License, version 3.

Victory-Road-Tracker is free RESTFUL API: you can use it under the terms
of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Victory-Road-Tracker is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with MindShare-API. If not, see <http://www.gnu.org/licenses/>.

This banner notice must not be removed.

-------------------------------------------------------------------------
 */

namespace model;

enum Element {
    case EARTH;
    case FIRE;
    case WIND;
    case WOOD;

    // -------------------------------------------------------------------------
    // STATIC METHODS
    // -------------------------------------------------------------------------

    public static function fromString(string $value): ?Element {
        return match (strtoupper($value)) {
            'EARTH' => Element::EARTH,
            'FIRE' => Element::FIRE,
            'WIND' => Element::WIND,
            'WOOD' => Element::WOOD,
            default => null,
        };
    }
}
