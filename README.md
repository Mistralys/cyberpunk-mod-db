![](docs/github_banner.jpg)

# Cyberpunk 2077 Clothing Mods Database

This is a curated database of Cyberpunk 2077 clothing mods, stored in individual 
JSON files and with custom screenshots for each mod.

## Live examples

- [Aeon's Cyberpunk Mods][]

## Data included in the database

- [JSON-based mod descriptions](data/clothing) with categorized items.
- [Mods overview](mods-list.md) shows all mods included in the database.
- [Virtual Atelier mods](docs/atelier-reference.md) used by the clothing mods.
- [Custom screenshots](data/clothing/screens) for each mod.
- [Pose pack references](docs/Poses/pose-reference.md) as screenshotting help.
- [Tags](docs/tagging-reference.md) used to categorize mods and items.

## Mods List

Total available mods: 185

See the [full list of included mods](mods-list.md).  

## Accessing the Database

At its simplest level, you can include the project as a submodule in your own
project, and load the JSON files manually.

The database was initially created to be used in a PHP project, so if you have 
access to PHP, you can use the separate project [Cyberpunk 2077 Clothing Mods Database PHP][].
This provides object-oriented access to the data, including a full text search.

## Contributing

Please see the [contribution guide](docs/contributing.md). I am always happy to 
receive error reports, new mods, tag suggestions, and any other kind of feedback.

## Documentation

- [Command line tools](docs/command-line-tools.md)
- [Pose Pack Editing](docs/pose-pack-editing.md)
- [Tagging Reference](docs/tagging-reference.md)

[Cyberpunk 2077 Clothing Mods Database PHP]: https://github.com/Mistralys/cyberpunk-mod-db-php
[Aeon's Cyberpunk Mods]: https://aeonoftime.com/?article=2024-08-06-cyberpunk-clothing-mods&page=article