# -*- coding: UTF-8 -*-
"""
:filename: scripts.db_stuffs_insert.py
:author:   Florian LOPITAUX
:contact:  florian.lopitaux@gmail.com
:summary:  script to fill the stuffs table of the database from the Excel file of 'Xeleko' YouTube channel.

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

API_URL = 'https://victoryroad-tracker.alwaysdata.net/api/stuff'
CSV_FILE = os.path.join("excel", "db_stuffs_xeleko.csv")

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


def get_category(value: str) -> str | None:
    match value.lower():
        case "chaussures":
            return "BOOTS"

        case "bracelet":
            return "BRACELET"

        case "pendentif":
            return "PENDANT"

        case "spécial":
            return "SPECIAL"

        case _:
            return None


def check_stats_cell(value: str) -> int | None:
    if len(value) == 0:
        return 0

    try:
        int_value = int(value)
    except ValueError:
        int_value = None

    return int_value


def get_stuff_stats(stats_data: dict) -> dict[str, int]:
    stats = dict()

    stats['kick'] = check_stats_cell(row['Frappe'])
    stats['control'] = check_stats_cell(row['Controle'])
    stats['pressure'] = check_stats_cell(row['Pression'])
    stats['physical'] = check_stats_cell(row['Physique'])
    stats['agility'] = check_stats_cell(row['Agilite'])
    stats['intelligence'] = check_stats_cell(row['Intelligence'])
    stats['technique'] = check_stats_cell(row['Technique'])

    return stats


# ---------------------------------------------------------------------------


if __name__ == "__main__":
    data = extract_csv_data(CSV_FILE)

    for i, row in enumerate(data):
        # extract and set item data
        item = dict()
        item['name'] = row['Nom Object']
        item['category'] = get_category(row['Type'])

        if item['category'] is None:
            print("-------------------------------------------------------------------------")
            print(f"Unknown category item : '{row['Type']}', for this item : {item['name']}")
            print("-------------------------------------------------------------------------")
            continue

        item['stats'] = dict()
        item['stats']['id'] = -1
        item['stats'].update(get_stuff_stats(row))

        # send post request to insert in the database
        response = requests.post(API_URL, json=item)

        if response.status_code == 200:
            print(f"{item['name']} correctly inserted !")
        else:
            print(f"Error during inserted this item : {item['name']}")
            print(f"Status code of the response : {response.status_code}")
            print(f"Traceback : {response.text}")
