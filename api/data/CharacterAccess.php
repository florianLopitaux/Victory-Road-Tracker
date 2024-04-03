<?php
/**
@file     api/data/DataAccess.php
@author   Florian Lopitaux
@version  0.1
@summary  Class to interact with the Character sql table and relations.

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

namespace apiData;

require_once 'api/data/DataAccess.php';

use model\{Character, Element, Hissatsu, PlayerRank, PlayerStats};
require_once 'model/Character.php';
require_once 'model/Element.php';

class CharacterAccess extends DataAccess {

    // -------------------------------------------------------------------------
    // PUBLIC METHODS
    // -------------------------------------------------------------------------

    public function getCharacter(string $name): Character | null {
        // send sql request
        $this->prepareQuery('SELECT * FROM Character WHERE name = ?');
        $this->executeQuery(array($name));

        // get the response
        $result = $this->getQueryResult();

        if (count($result) > 0) {
            $entity = $result[0];
            $character = new Character($entity['name'], Element::fromString($entity['element']), $entity['level']);
            $this->setCharacterHissatsu($character);
            $this->setCharacterStats($character);

            return $character;

        } else {
            return null;
        }
    }


    // -------------------------------------------------------------------------
    // PRIVATE METHODS
    // -------------------------------------------------------------------------

    private function setCharacterHissatsu(Character $character): void {
        // send sql request
        $this->prepareQuery('SELECT * FROM Hissatsu, Owned JOIN Owned ON Hissatsu.name = Owned.hisstasu_name WHERE Owned.character_name = ?');
        $this->executeQuery(array($character->getName()));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $character->addHissatsu(Hissatsu::fromArray($entity), $entity['level_unlocked']);
        }
    }

    // -------------------------------------------------------------------------

    private function setCharacterStats(Character $character): void {
        // send sql request
        $this->prepareQuery('SELECT * FROM Statistique, PlayerStats JOIN PlayerStats ON Statistique.id = PlayerStats.idStats WHERE PlayerStats.character_name = ?');
        $this->executeQuery(array($character->getName()));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $character->setStats(PlayerRank::fromString($entity['player_rank']), PlayerStats::fromArray($entity));
        }
    }
}
