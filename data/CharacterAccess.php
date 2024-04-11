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
        $this->prepareQuery('SELECT * FROM Characters WHERE name = ?');
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
        $this->prepareQuery('SELECT * FROM Characters');
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
        $this->prepareQuery('SELECT * FROM Characters WHERE element = ?');
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
        $this->prepareQuery('SELECT COUNT(*) FROM Characters WHERE name = ?');
        $this->executeQuery(array($character->getName()));

        if (count($this->getQueryResult()) > 0) {
            return false;
        }

        // insert in character model
        $this->prepareQuery('INSERT INTO Characters VALUES (?, ?, ?)');
        $this->executeQuery(array($character->getName(), $character->getElement()->name, $character->getLevel()));
        $this->closeQuery();

        // insert in hissatsu relation table
        foreach ($character->getHissatsu() as $hissatsuData) {
            $this->prepareQuery('INSERT INTO CharacterHissatsu VALUES (?, ?, ?)');
            $this->executeQuery(array($character->getName(), $hissatsuData[1]->getName(), $hissatsuData[0]));
            $this->closeQuery();
        }

        foreach ($character->getStats() as $rank => $stats) {
            // check if statistic entity exist and create it isn't
            $id_stats = $stats->getID();

            if ($id_stats === -1) {
                $this->prepareQuery('INSERT INTO Statistics (`kick`, `control`, `pressure`, `physical`, `agility`, `intelligence`, `technique`) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $this->executeQuery($stats->toArray(false, false));
                $id_stats = $this->getLastIDInserted();

            }  else {
                $this->prepareQuery('SELECT COUNT(*) FROM Statistics WHERE id = ?');
                $this->executeQuery(array($id_stats));

                if ($this->getQueryResult()[0] === 0) {
                    $this->prepareQuery('INSERT INTO Statistics VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                    $this->executeQuery($stats->toArray());
                }
            }
            $this->closeQuery();

            // insert in statistic relation table
            $this->prepareQuery('INSERT INTO CharacterStats VALUES (?, ?, ?)');
            $this->executeQuery(array($character->getName(), $id_stats, $rank));
            $this->closeQuery();
        }

        return true;
    }

    // -------------------------------------------------------------------------

    public function deleteCharacter(string $name): bool {
        // check if the character exists
        $this->prepareQuery('SELECT COUNT(*) FROM Characters WHERE name = ?');
        $this->executeQuery(array($name));

        if (count($this->getQueryResult()) === 0) {
            return false;
        }

        // delete character entity
        $this->prepareQuery('DELETE FROM Characters WHERE name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        // delete relations with other tables
        $this->prepareQuery('DELETE FROM CharacterHissatsu WHERE character_name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        $this->prepareQuery('DELETE FROM CharacterStats WHERE character_name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        return true;
    }


    // -------------------------------------------------------------------------
    // PRIVATE METHODS
    // -------------------------------------------------------------------------

    private function setCharacterHissatsu(Character $character): void {
        // send sql request
        $this->prepareQuery('SELECT * FROM Hissatsu JOIN CharacterHissatsu ON Hissatsu.name = CharacterHissatsu.hisstasu_name WHERE CharacterHissatsu.character_name = ?');
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
        $this->prepareQuery('SELECT * FROM Statistics JOIN CharacterStats ON Statistics.id = CharacterStats.idStats WHERE CharacterStats.character_name = ?');
        $this->executeQuery(array($character->getName()));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $character->setStats(PlayerRank::fromString($entity['player_rank']), Statistics::fromArray($entity));
        }
    }
}
