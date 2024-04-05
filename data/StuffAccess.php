<?php
/**
 * @file     data/StuffAccess.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class to interact with the Stuff sql table and relations.
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

use model\{Statistics, Stuff, StuffCategory};
require_once 'model/Statistics.php';
require_once 'model/Stuff.php';
require_once 'model/StuffCategory.php';

class StuffAccess extends DataAccess {

    // -------------------------------------------------------------------------
    // PUBLIC METHODS
    // -------------------------------------------------------------------------

    public function getStuff(string $name): Stuff | null {
        // send sql server
        $this->prepareQuery('SELECT * FROM Stuff JOIN Statistics ON Stuff.id_stats = Statistics.id WHERE Stuff.name = ?');
        $this->executeQuery(array($name));

        // get the response
        $result = $this->getQueryResult();

        if (count($result) > 0) {
            $entity = $result[0];
            return new Stuff($name, StuffCategory::fromString($entity['category']), Statistics::fromArray($entity));

        } else {
            return null;
        }
    }

    // -------------------------------------------------------------------------

    public function getCategoryStuffs(StuffCategory $category): array {
        $stuffs = array();

        // send sql server
        $this->prepareQuery('SELECT * FROM Stuff JOIN Statistics ON Stuff.id_stats = Statistics.id WHERE Stuff.category = ?');
        $this->executeQuery(array($category->name));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $stuffs[] = new Stuff($entity['name'], $category, Statistics::fromArray($entity));
        }

        return $stuffs;
    }

    // -------------------------------------------------------------------------

    public function getAllStuff(): array {
        $allStuff = array();

        // send sql server
        $this->prepareQuery('SELECT * FROM Stuff JOIN Statistics ON Stuff.id_stats = Statistics.id');
        $this->executeQuery(array());

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $allStuff[] = new Stuff($entity, StuffCategory::fromString($entity['category']), Statistics::fromArray($entity));
        }

        return $allStuff;
    }

    // -------------------------------------------------------------------------

    public function insertStuff(Stuff $stuff): bool {
        // check if the stuff already exists
        $this->prepareQuery('SELECT COUNT(*) FROM Stuff WHERE name = ?');
        $this->executeQuery(array($stuff->getName()));

        if (count($this->getQueryResult()) > 0) {
            return false;
        }

        // send sql request
        $this->prepareQuery('INSERT INTO Statistics VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $this->executeQuery($stuff->getStats()->toArray());
        $this->closeQuery();

        $this->prepareQuery('INSERT INTO Stuff VALUES (?, ?, ?)');
        $this->executeQuery(array($stuff->getName(), $stuff->getCategory()->name, $stuff->getStats()->getID()));
        $this->closeQuery();

        return true;
    }

    // -------------------------------------------------------------------------

    public function deleteStuff(Stuff $stuff): bool {
        // check if the stuff exists
        $this->prepareQuery('SELECT COUNT(*) FROM Stuff WHERE name = ?');
        $this->executeQuery(array($stuff->getName()));

        if (count($this->getQueryResult()) === 0) {
            return false;
        }

        // delete entity
        $this->prepareQuery('DELETE FROM Stuff WHERE name = ?');
        $this->executeQuery(array($stuff->getName()));
        $this->closeQuery();

        $this->prepareQuery('DELETE FROM Statistics WHERE id = ?');
        $this->executeQuery(array($stuff->getStats()->getID()));
        $this->closeQuery();

        return true;
    }
}
