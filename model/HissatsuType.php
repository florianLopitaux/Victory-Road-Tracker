<?php
/**
@file     model/HissatsuType.php
@author   Florian Lopitaux
@version  0.1
@summary  Enumeration class that contains the different types of the hissatsu techniques.

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

enum HissatsuType: string {
    case CATCH = 'CATCH';
    case DEFENSE = 'DEFENSE';
    case OFFENSE = 'OFFENSE';
    case SHOOT = 'SHOOT';

    // -------------------------------------------------------------------------
    // STATIC METHODS
    // -------------------------------------------------------------------------

    public static function from_string(string $value): ?HissatsuType {
        return match ($value) {
            'CATCH' => HissatsuType::CATCH,
            'DEFENSE' => HissatsuType::DEFENSE,
            'OFFENSE' => HissatsuType::OFFENSE,
            'SHOOT' => HissatsuType::SHOOT,
            default => null,
        };
    }
}
