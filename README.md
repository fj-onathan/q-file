 ## 📝 Q-File! 
 
Query File is basicaly a lightweight PHP class for queries (retrieve/select/where/order) custom data from a flat file.
File is pretty to learn, not like JSON, or ARRAY, or SQL, all level of developers can learn easy that typo of files.

## Installation

### Composer

Install with a command:

```bash
composer require fjonathan/q-file
```

## Configuration

Buil the 'table' for using data to output and filter.
Create a file like `table.ay` and build your own custom data

That's a example:

```
new "groups"

    (Google)
        [id: 1]

    (Whatsapp)
        [id: 2]

new "contact_list"

    (Jean Doe)
        [group: 2]
        [email: jeandoe@email.com]
        [avatar: user1.png]
        [address: 2722 Retreat Avenue]
        [city: Los Angeles]
        [phone: 562-567-3643]

    (Michel Doe)
        [group: 1]
        [email: micheldoe@email.com]
        [avatar: user2.png]
        [address: 577 Carolina Avenue]
        [city: Fort Collins]
        [phone: 808-933-9356]

```

## Usage

Init package to starting using it

```php
use File\Q;
$f = new Q;
$table = 'table.ay';
```

Return all data organized by keys

```php
$data = $f
        ->table($table)
        ->record();
```

## Options

These light class has many options to filter data, like:

- `searchAll` - search by primary table content - `->searchAll('groups')`
- `searchby` - search by key group - `->searchBy('contact_list', 'group', 1)`
- `orderBy` - order by asc/desc the results data -> `orderBy('id', 'ASC')`

### Notes

This tool has been retrieved from an old project, and may not be constant updates, so use with care.
