<?php
/**
 * @file     data/HissatsuAccess.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class to interact with the Hissatsu sql table and relations.
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
require_once 'data/CharacterAccess.php';

use model\Hissatsu;
require_once 'model/Hissatsu.php';

final class HissatsuAccess extends DataAccess {

    // -------------------------------------------------------------------------
    // PUBLIC METHODS
    // -------------------------------------------------------------------------

    public function getHissatsu(string $name): Hissatsu | null {
        // send sql request
        $this->prepareQuery('SELECT * FROM Hissatsu WHERE name = ?');
        $this->executeQuery(array($name));

        // get the response
        $result = $this->getQueryResult();

        if (count($result) > 0) {
            return Hissatsu::fromArray($result[0]);
        } else {
            return null;
        }
    }

    // -------------------------------------------------------------------------

    public function getAllHissatsu(): array {
        $hissatsu = array();

        // send sql request
        $this->prepareQuery('SELECT * FROM Hissatsu');
        $this->executeQuery(array());

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $hissatsu[] = Hissatsu::fromArray($entity);
        }

        return $hissatsu;
    }

    // -------------------------------------------------------------------------

    public function getHissatsuOwners(CharacterAccess $characterAccess, string $hissatsuName): array {
        $characters = array();

        // send sql request
        $this->prepareQuery('SELECT Character.name FROM Character JOIN Master ON Character.name = Master.character_name WHERE Master.hissatsu_name = ?');
        $this->executeQuery(array($hissatsuName));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $characters[] = $characterAccess->getCharacter($entity['name']);
        }

        return $characters;
    }

    // -------------------------------------------------------------------------

    public function insertHissatsu(Hissatsu $hissatsu): bool {
        // check if the hissatsu already exists
        $this->prepareQuery('SELECT Count(*) FROM Hissatsu WHERE name = ?');
        $this->executeQuery(array($hissatsu->getName()));

        if (count($this->getQueryResult()) > 0) {
            return false;
        }

        // send sql request
        $this->prepareQuery('INSERT INTO Hissatsu VALUES (?, ?, ?, ?)');
        $this->executeQuery($hissatsu->toArray());

        $this->closeQuery();
        return true;
    }

    // -------------------------------------------------------------------------

    public function deleteHissatsu(string $name): bool {
        // check if the hissatsu exists
        $this->prepareQuery('SELECT Count(*) FROM Hissatsu WHERE name = ?');
        $this->executeQuery(array($name));

        if (count($this->getQueryResult()) === 0) {
            return false;
        }

        // delete hissatsu entity
        $this->prepareQuery('DELETE FROM Hissatsu WHERE name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        // delete relations with Character table
        $this->prepareQuery('DELETE FROM Master WHERE hisstasu_name = ?');
        $this->executeQuery(array($name));
        $this->closeQuery();

        return true;
    }
}
