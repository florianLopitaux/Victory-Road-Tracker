<?php
/**
 * @file     model/Statistics.php
 * @author   Florian Lopitaux
 * @version  0.1
 * @summary  Class that represents the statistics of a character.
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

namespace model;

class Statistics {

    // -------------------------------------------------------------------------
    // FIELDS
    // -------------------------------------------------------------------------

    private int $id;
    private int $kick;
    private int $control;
    private int $pressure;
    private int $agility;
    private int $physical;
    private int $intelligence;
    private int $technique;


    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------

    public function __construct(int $id, int $kick, int $control, int $pressure, int $physical, int $agility, int $intelligence, int $technique) {
        $this->id = $id;
        $this->kick = $kick;
        $this->control = $control;
        $this->pressure = $pressure;
        $this->physical = $physical;
        $this->agility = $agility;
        $this->intelligence = $intelligence;
        $this->technique = $technique;
    }


    // -------------------------------------------------------------------------
    // GETTERS
    // -------------------------------------------------------------------------

    public function getID(): int {
        return $this->id;
    }

    // -------------------------------------------------------------------------

    public function getKick(): int {
        return $this->kick;
    }

    // -------------------------------------------------------------------------

    public function getControl(): int {
        return $this->control;
    }

    // -------------------------------------------------------------------------

    public function getPressure(): int {
        return $this->pressure;
    }

    // -------------------------------------------------------------------------

    public function getPhysical(): int {
        return $this->physical;
    }

    // -------------------------------------------------------------------------

    public function getAgility(): int {
        return $this->agility;
    }

    // -------------------------------------------------------------------------

    public function getIntelligence(): int {
        return $this->intelligence;
    }

    // -------------------------------------------------------------------------

    public function getTechnique(): int {
        return $this->technique;
    }


    // -------------------------------------------------------------------------
    // SETTERS
    // -------------------------------------------------------------------------

    public function setKick(int $kick): void {
        $this->kick = $kick;
    }

    // -------------------------------------------------------------------------

    public function setControl(int $control): void {
        $this->control = $control;
    }

    // -------------------------------------------------------------------------

    public function setPressure(int $pressure): void {
        $this->pressure = $pressure;
    }

    // -------------------------------------------------------------------------

    public function setPhysical(int $physical): void {
        $this->physical = $physical;
    }

    // -------------------------------------------------------------------------

    public function setAgility(int $agility): void {
        $this->agility = $agility;
    }

    // -------------------------------------------------------------------------

    public function setIntelligence(int $intelligence): void {
        $this->intelligence = $intelligence;
    }

    // -------------------------------------------------------------------------

    public function setTechnique(int $technique): void {
        $this->technique = $technique;
    }


    // -------------------------------------------------------------------------
    // PUBLIC METHODS
    // -------------------------------------------------------------------------

    public function toArray(bool $with_keys = false, bool $with_id = true): array {
        if ($with_keys) {
            $object = array(
                'kick' => $this->kick,
                'control' => $this->control,
                'pressure' => $this->pressure,
                'physical' => $this->physical,
                'agility' => $this->agility,
                'intelligence' => $this->intelligence,
                'technique' => $this->technique
            );

            if ($with_id) {
                $object['id'] = $this->id;
            }

        } else {
            $object = array(
                $this->kick,
                $this->control,
                $this->pressure,
                $this->physical,
                $this->agility,
                $this->intelligence,
                $this->technique
            );

            if ($with_id) {
                array_unshift($object, $this->id);
            }
        }

        return $object;
    }

    // -------------------------------------------------------------------------

    public function total(): int {
        $sum = 0;

        foreach ($this as $stat => $value) {
            if ($value != null) {
                $sum += $value;
            }
        }

        return $sum;
    }

    // -------------------------------------------------------------------------

    public function computeATShoot(): int {
        return $this->kick + $this->control;
    }

    // -------------------------------------------------------------------------

    public function computeATFocus(): int {
        return $this->control + $this->technique;
    }

    // -------------------------------------------------------------------------

    public function computeATDuel(): int {
        return $this->physical + $this->intelligence;
    }

    // -------------------------------------------------------------------------

    public function computeDEFWall(): int {
        return $this->pressure + $this->physical;
    }

    // -------------------------------------------------------------------------

    public function computeDEFFocus(): int {
        return $this->intelligence + $this->technique;
    }

    // -------------------------------------------------------------------------

    public function computeDEFDuel(): int {
        return $this->pressure + $this->intelligence;
    }

    // -------------------------------------------------------------------------

    public function computeGK(): int {
        return $this->physical + $this->agility;
    }

    // -------------------------------------------------------------------------
    // STATIC METHODS
    // -------------------------------------------------------------------------

    public static function fromArray(array $entity): Statistics {
        if (in_array('id', $entity)) {
            $id = $entity['id'];
        } else {
            $id = -1;
        }

        return new Statistics(
            $id,
            $entity['kick'],
            $entity['control'],
            $entity['pressure'],
            $entity['physical'],
            $entity['agility'],
            $entity['intelligence'],
            $entity['technique']
        );
    }
}
