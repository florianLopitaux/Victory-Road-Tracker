<?php
/**
@file     model.Character.php
@author   Florian Lopitaux
@version  0.1
@summary  Class that represents a character in the game.

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

require_once 'model/Element.php';
require_once 'model/PlayerRank.php';
require_once 'model/PlayerStats.php';
require_once 'model/Hissatsu.php';

class Character {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private string $name;
    private Element $element;
    private int $level;
    private array $hissatsu;
    private array $stats;


    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(string $name, Element $element, int $level) {
        $this->name = $name;
        $this->element = $element;
        $this->level = $level;

        $this->hissatsu = array();
        $this->stats = array(
            PlayerRank::NORMAL->name => null,
            PlayerRank::RARE->name => null,
            PlayerRank::ADVANCED->name => null,
            PlayerRank::TOP->name => null,
            PlayerRank::LEGENDARY->name => null,
        );
    }


    // -------------------------------------------------------------------------
    // GETTERS
    // -------------------------------------------------------------------------

    public function getName(): string {
        return $this->name;
    }

    // -------------------------------------------------------------------------

    public function getElement(): Element {
        return $this->element;
    }

    // -------------------------------------------------------------------------

    public function getLevel(): int {
        return $this->level;
    }

    // -------------------------------------------------------------------------

    public function getHissatsu(string $hissatsuName = null): null | array {
        if ($hissatsuName == null) {
            return $this->hissatsu;
        } else {
            foreach ($this->hissatsu as $values) {
                if ($hissatsuName === $values[1]->getName()) {
                    return $values;
                }
            }

            return null;
        }
    }

    // -------------------------------------------------------------------------

    public function getStats(PlayerRank $rank = null): null | array | PlayerStats {
        if ($rank == null) {
            return $this->stats;
        } else {
            return $this->stats[$rank->name];
        }
    }


    // -------------------------------------------------------------------------
    // SETTERS
    // -------------------------------------------------------------------------

    public function addHissatsu(Hissatsu $hissatsu, int $level = null): void {
        $this->hissatsu[] = array($level, $hissatsu);
    }

    // -------------------------------------------------------------------------

    public function setStats(PlayerRank $rank, PlayerStats $stats): void {
        $this->stats[$rank->name] = $stats;
    }
}
