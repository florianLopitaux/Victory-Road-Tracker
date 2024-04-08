# VictoryRoad-Tracker API
RESTFUL API to access to the database of the VictoryRoad-Tracker application. <br/>
The API is accessible in this url : https://victoryroad-tracker.alwaysdata.net/api/ <br/>
Some requests are protected, they need the private bearer token in the header Authorization of the request.

## Requests List

### GET

> - /character  -- get all characters.
> - /character/{name}  -- get a specific character by its name.
> - /character/element/{value} -- get all characters of a specific element (possible values : earth, fire, wind, wood).

<br/>

> - /hissatsu  -- get all hissatsu
> - /hissatsu/{name}  -- get a specific hissatsu by its name.
> - /hissatsu/element/{value}  -- get all hisstatsu of a specific element (possible values : earth, fire, wind, wood).
> - /hissatsu/type/{value}  -- get all hisstatsu of a specific type (possible values : catch, defense, offense, shoot).
> - /hissatsu/characters/{hissatsu_name}  -- get all characters that possessing a specific hissatsu technique.

<br/>

> - /stuff  -- get all stuffs.
> - /stuff/{name}  -- get a specific stuff by its name.
> - /stuff/category/{value}  -- get all stuffs of a specific category (possible values : boots, bracelet, pendant, special).

<br/>

### POST (required bearer token authorization)

> - /character |||| **[character model]**  -- create a new character.

<br/>

> - /hissatsu |||| **[hissatsu model]**  -- create a new hissatsu.
> - /hissatsu/character |||| {'character_name' => **[name of the character]**, 'hissatsu_name' => **[name of the hissatsu]**, 'level_unlocked' => **[level when the character unlocks the hisstasu]**}  -- insert a new relation between a character and a hissatsu, means that the character knows that hissatsu.

<br/>

> - /stuff |||| **[stuff model]**  -- create a new stuff.

<br/>

### DELETE (required bearer token authorization)

> - /character/{name}  -- delete a specific character by its name.

> - /hissatsu/{name}  -- delete a specific hissatsu by it's name.

> - /stuff/{name}  -- delete a specific stuff by its name.
