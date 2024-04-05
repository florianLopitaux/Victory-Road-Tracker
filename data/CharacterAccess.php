<?php
/**
 * @file     data/CharacterAccess.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class to interact with the Character sql table and relations.
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

namespace data;

require_once 'data/DataAccess.php';

use model\{Character, Element, Hissatsu, PlayerRank, Statistics};
require_once 'model/Character.php';
require_once 'model/Element.php';
require_once 'model/Hissatsu.php';
require_once 'model/PlayerRank.php';
require_once 'model/Statistics.php';


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

    public function getAllCharacters(): array {
        $characters = array();

        // send sql request
        $this->prepareQuery('SELECT * FROM Character');
        $this->executeQuery(array());

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $current = new Character($entity['name'], Element::fromString($entity['element']), $entity['level']);
            $this->setCharacterHissatsu($current);
            $this->setCharacterStats($current);

            $characters[] = $current;
        }

        return $characters;
    }

    // -------------------------------------------------------------------------

    public function getElementCharacters(Element $element): array {
        $characters = array();

        // send sql request
        $this->prepareQuery('SELECT * FROM Character WHERE element = ?');
        $this->executeQuery(array($element->name));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $current = new Character($entity['name'], Element::fromString($entity['element']), $entity['level']);
            $this->setCharacterHissatsu($current);
            $this->setCharacterStats($current);

            $characters[] = $current;
        }

        return $characters;
    }

    // -------------------------------------------------------------------------

    public function insertCharacter(Character $character): bool {
        // check if the character already exists
        $this->prepareQuery('SELECT COUNT(*) FROM Character WHERE name = ?');
        $this->executeQuery(array($character->getName()));

        if (count($this->getQueryResult()) > 0) {
            return false;
        }

        // send sql request
        $this->prepareQuery('INSERT INTO Character VALUES (?, ?, ?)');
        $this->executeQuery(array($character->getName(), $character->getElement()->name, $character->getLevel()));
        $this->closeQuery();

        foreach ($character->getHissatsu() as $hissatsuData) {
            $this->prepareQuery('INSERT INTO Master VALUES (?, ?, ?)');
            $this->executeQuery(array($character->getName(), $hissatsuData[1]->getName(), $hissatsuData[0]));
            $this->closeQuery();
        }

        foreach ($character->getStats() as $rank => $stats) {
            $this->prepareQuery('INSERT INTO Statistics VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $this->executeQuery($stats->toArray());
            $this->closeQuery();

            $this->prepareQuery('INSERT INTO PlayerStats VALUES (?, ?, ?)');
            $this->executeQuery(array($character->getName(), $stats->getID(), $rank));
            $this->closeQuery();
        }

        return true;
    }

    // -------------------------------------------------------------------------

    public function deleteCharacter(string $name): bool {
        // check if the character exists
        $this->prepareQuery('SELECT COUNT(*) FROM Character WHERE name = ?');
        $this->executeQuery(array($name));

        if (count($this->getQueryResult()) === 0) {
            return false;
        }

        // delete character entity
        $this->prepareQuery('DELETE FROM Character WHERE name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        // delete relations with other tables
        $this->prepareQuery('DELETE FROM Master WHERE character_name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        $this->prepareQuery('DELETE FROM PlayerStats WHERE character_name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        return true;
    }


    // -------------------------------------------------------------------------
    // PRIVATE METHODS
    // -------------------------------------------------------------------------

    private function setCharacterHissatsu(Character $character): void {
        // send sql request
        $this->prepareQuery('SELECT * FROM Hissatsu JOIN Master ON Hissatsu.name = Master.hisstasu_name WHERE Master.character_name = ?');
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
        $this->prepareQuery('SELECT * FROM Statistics JOIN PlayerStats ON Statistique.id = PlayerStats.idStats WHERE PlayerStats.character_name = ?');
        $this->executeQuery(array($character->getName()));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $character->setStats(PlayerRank::fromString($entity['player_rank']), Statistics::fromArray($entity));
        }
    }
}
