<?php
/**
@file     api/data/DataAccess.php
@author   Florian Lopitaux
@version  0.1
@summary  Class to interact with the Hissatsu sql table and relations.

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

    public function getHissatsuOwners(string $hissatsuName): array {
        $characters = array();

        // send sql request
        $this->prepareQuery('SELECT Character.name FROM Character JOIN Owned ON Character.name = Owned.character_name WHERE Owned.hissatsu_name = ?');
        $this->executeQuery(array($hissatsuName));

        // get the response
        $result = $this->getQueryResult();

        foreach ($result as $entity) {
            $characters[] = $entity['name'];
        }

        return $characters;
    }
}
