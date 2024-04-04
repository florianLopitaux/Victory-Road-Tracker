<?php
/**
@file     model.Hissatsu.php
@author   Florian Lopitaux
@version  0.1
@summary  Class that represents a hissatsu technique.

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

require_once 'model/HissatsuType.php';
require_once 'model/Element.php';

class Hissatsu {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private string $name;
    private HissatsuType $type;
    private Element $element;
    private int $power;


    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(string $name, HissatsuType $type, Element $element, int $power) {
        $this->name = $name;
        $this->type = $type;
        $this->element = $element;
        $this->power = $power;
    }


    // -------------------------------------------------------------------------
    // GETTERS
    // -------------------------------------------------------------------------

    public function getName(): string {
        return $this->name;
    }

    // -------------------------------------------------------------------------

    public function getType(): HissatsuType {
        return $this->type;
    }

    // -------------------------------------------------------------------------

    public function getElement(): Element {
        return $this->element;
    }

    // -------------------------------------------------------------------------

    public function getPower(): int {
        return $this->power;
    }


    // -------------------------------------------------------------------------
    // SETTERS
    // -------------------------------------------------------------------------

    public function setPower(int $power): void {
        $this->power = $power;
    }


    // -------------------------------------------------------------------------
    // PUBLIC METHODS
    // -------------------------------------------------------------------------

    public function toArray(): array {
        return array(
            $this->name,
            $this->type->name,
            $this->element->name,
            $this->power
        );
    }

    // -------------------------------------------------------------------------
    // STATIC METHODS
    // -------------------------------------------------------------------------

    public static function fromArray(array $entity): Hissatsu {
        return new Hissatsu(
            $entity['name'],
            HissatsuType::fromString($entity['type']),
            Element::fromString($entity['element']),
            $entity['power']
        );
    }
}
