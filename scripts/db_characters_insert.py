# -*- coding: UTF-8 -*-
"""
:filename: scripts.db_characters_insert.py
:author:   Florian LOPITAUX
:contact:  florian.lopitaux@gmail.com
:summary:  script to fill the Characters table of the database from the Excel file of 'Xeleko' YouTube channel.

.. _This file is part of Victory-Road-Tracker: https://github.com/florianLopitaux/Victory-Road-Tracker
..
    -------------------------------------------------------------------------

    Copyright (C) 2024 Florian LOPITAUX

    Use of this software is governed by the GNU Public License, version 3.

    Victory-Road-Tracker is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Victory-Road-Tracker is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Victory-Road-Tracker. If not, see <https://www.gnu.org/licenses/>.

    This banner notice must not be removed.

    -------------------------------------------------------------------------

"""

import os
import csv
import requests

# ---------------------------------------------------------------------------

API_URL = 'https://victoryroad-tracker.alwaysdata.net/api/character'
CSV_FILE = os.path.join("excel", "db_characters_xeleko.csv")

# ---------------------------------------------------------------------------


def extract_csv_data(filepath: str) -> list[dict]:
    if not os.path.exists(filepath):
        raise FileNotFoundError(f"Doesn't found csv file : {filepath}")

    csv_data = list()

    with open(filepath, 'r') as csv_file:
        csv_reader = csv.DictReader(csv_file)

        for line in csv_reader:
            csv_data.append(line)

    return csv_data


def get_rank_from_index(index_value: int) -> str:
    match index_value % 5:
        case 0:
            return "NORMAL"
        case 1:
            return "RARE"
        case 2:
            return "ADVANCED"
        case 3:
            return "TOP"
        case 4:
            return "LEGENDARY"


def check_stats_cell(value: str) -> int | None:
    if len(value) == 0:
        return None

    try:
        int_value = int(value)
    except ValueError:
        int_value = None

    return int_value


def get_character_stats(stats_data: dict) -> dict[str, int]:
    stats = dict()

    stats['kick'] = check_stats_cell(stats_data['Frappe'])
    stats['control'] = check_stats_cell(stats_data['Controle'])
    stats['pressure'] = check_stats_cell(stats_data['Pression'])
    stats['physical'] = check_stats_cell(stats_data['Physique'])
    stats['agility'] = check_stats_cell(stats_data['Agilite'])
    stats['intelligence'] = check_stats_cell(stats_data['Intelligence'])
    stats['technique'] = check_stats_cell(stats_data['Technique'])

    return stats


def send_post_request(body: dict) -> None:
    response = requests.post(API_URL, json=body)

    if response.status_code == 200:
        print(f"{body['name']} correctly inserted !")
    else:
        print(f"Error during inserted this character : {body['name']}")
        print(f"Status code of the response : {response.status_code}")
        print(f"Traceback : {response.text}")


# ---------------------------------------------------------------------------


if __name__ == "__main__":
    data = extract_csv_data(CSV_FILE)

    character = dict()
    for i, row in enumerate(data):
        # extract and set character data
        character_name = row['Nom Joueur']

        # get base data of character first time we find them
        if character_name != character.get('name', ""):
            send_post_request(character)

            character = dict()
            character['name'] = character_name
            character['element'] = row['Elem.']
            character['level'] = 30
            character['stats'] = dict()
            character['hissatsu'] = [
                [-1, row['Technique 1']],
                [-1, row['Technique 2']]
            ]

        rank = get_rank_from_index(i)

        character['stats'][rank] = {'id': -1}
        character['stats'][rank].update(get_character_stats(row))
