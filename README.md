![](docs/github_banner.jpg)

# Cyberpunk 2077 Clothing Mods Database

This is a curated database of detailed Cyberpunk 2077 clothing 
mod descriptions stored in individual JSON files, and with custom 
screenshots.

## Live examples

- [Aeon's Cyberpunk Mods][]

## Data included in the database

- [JSON-based mod descriptions](data/clothing) with categorized items.
- [Mods overview](mods-list.md) shows all mods included in the database.
- [Virtual Atelier mods](docs/atelier-reference.md) used by the clothing mods.
- [Custom screenshots](data/clothing/screens) for each mod.
- [Pose pack references](docs/Poses/pose-reference.md) as screenshotting help.
- [Tags](docs/tagging-reference.md) used to categorize mods and items.

## Accessing the Database

At its simplest level, you can include the project as a submodule in your own
project, and load the JSON files manually.

The database was initially created to be used in a PHP project, so if you have 
access to PHP, you can use the separate project [Cyberpunk 2077 Clothing Mods Database PHP][]
for object-oriented access to the data. This includes a full text search, which
is not available out of the box here.

> NOTE: GitHub shows the project as a PHP project, but those PHP files are only
> what I use as helpers to author the database. The main database parts are the 
> JSON files and the screenshots.

## Contributing

Please see the [contribution guide](docs/contributing.md). I am always happy to 
receive error reports, new mods, tag suggestions, and any other kind of feedback.

## Documentation

- [Command line tools](docs/command-line-tools.md)
- [Pose Pack Editing](docs/pose-pack-editing.md)
- [Tagging Reference](docs/tagging-reference.md)

[Cyberpunk 2077 Clothing Mods Database PHP]: https://github.com/Mistralys/cyberpunk-mod-db-php
[Aeon's Cyberpunk Mods]: https://aeonoftime.com/?article=2024-08-06-cyberpunk-clothing-mods&page=article