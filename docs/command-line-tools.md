# Command Line Tools

A small selection of command line tools are available in the
database to help with managing the mod data, and preparing 
releases.

## Requirements

- PHP 8.2 or higher
- Composer

## Installation

1. Clone the repository
2. Run `composer install`

## Usage

The typical workflow for adding a new mod is:

1. Create the mod's JSON file
2. Add all the relevant data
3. Normalize the file
4. Add a screenshot
5. Push the changes / create a pull request

## Commands

### Adding a new mod

This will create an empty JSON skeleton file for a new mod.

```bash
php bin/cpmdb.php mod="mod-id" create
```

Optional: Setting the mod's name.

```bash
php bin/cpmdb.php mod="mod-id" create="Mod Name"
```


### Adding an item category

This will append a new item category to the target mod.

```bash
php bin/cpmdb.php mod="mod-id" add-category
```

Optional: Setting the category label. 

```bash
php bin/cpmdb.php mod="mod-id" add-category="Category name"
```

### Normalizing a mod

This will normalize the mod's JSON file, ensuring all required 
keys are present, sorting tags and other values alphabetically
to ensure that they are consistent, and to avoid bloating the
version control history with unnecessary changes.

```bash
php bin/cpmdb.php mod="mod-id" normalize
```

### Showing the CET item codes

This will display a list of commands for the CET console to add
all the mod's items to the player's inventory.

```bash
php bin/cpmdb.php mod="mod-id" cet-codes
````

### Adding a screenshot

Screenshots must be in JPG format and stored under:

`data/clothing/screens` 

They must be named after the mod's ID.

### Generating the mod list

This will update the `mods-list.md` file with the current list of mods.

```bash
php bin/cpmdb.php modlist
```

### Adding more screenshots

As soon as a mod has more than one screenshot, the additional ones
must be registered in a separate JSON file. This command can create
the file for you.

```bash
php bin/cpmdb.php mod="{mod-id}" add-screenshot="{screen-id}"
```

See [contributing mods](contributing-mods.md) for more details on
setting up multiple screenshots.

### Building a release

Building / preparing the release involves:

1. Normalizing all mod files
2. Generating the mod list
3. Generating the atelier reference
4. Generating the tag reference

The end of the build process will display a list of 
messages and recommendations detected in the files.

```bash
php bin/cpmdb.php build
```
