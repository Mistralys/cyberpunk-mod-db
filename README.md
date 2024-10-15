# Cyberpunk 2077 Clothing Mods Database

Curated database of Cyberpunk 2077 clothing mods, stored in individual JSON files
and with custom screenshots for each mod.

## Live examples

- [Aeon's Cyberpunk Mods][]

## Mods List

See the [full list of included mods](mods-list.md).  

## Accessing the Database

At its simplest level, you can include the project as a submodule in your own
project, and load the JSON files manually. The database was initially created
to be used in a PHP project however, so if you have access to PHP, you can
use the separate project [Cyberpunk 2077 Clothing Mods Database PHP][].

## Submitting JSON files

Feel free to contribute to this list by writing JSON files for your favorite
clothing mods, and making a pull request for them. 

**A note on screenshots:**

Right now, the screenshots are done by me. I plan to add a guide to make it 
possible for contributors to add their own screenshots in the future. My aim
is to keep the visuals consistent, so I will provide a guide on how to create
screenshots that match the existing ones.

The JSON format is quite simple:

```json
{
  "mod": "Mod Name",
  "url": "https://www.nexusmods.com/cyberpunk2077/mods/1234",
  "atelier": "https://www.nexusmods.com/cyberpunk2077/mods/1234",
  "authors": [
    "Author 1", 
    "Author 2"
  ],
  "comments": "Optional comments about the mod.",
  "tags": [
    "AXL", 
    "EQEX", 
    "TXL", 
    "CDW", 
    "ATL", 
    "R4EX"
  ],
  "itemCategories": [
    {
      "label": "Category name",
      "tags": [
        "Clothing" 
      ],
      "items": [
        {
          "name": "Item name",
          "code": "item_code",
          "tags": [
            "Emissive"
          ]
        }
      ]
    }
  ]
}
```

- `mod`: The name of the mod.
- `url`: The URL to the mod on Nexus Mods or other mods site (must be publicly available).
- `atelier`: The URL to the mod's Virtual Atelier mod (if any).
- `authors`: A list of authors of the mod.
- `tags`: A list of tags identifying dependencies to other mods and the kind of items. See [Tags Legend](#tags-legend) for a list.
- `comments`: Optional comments about the mod.
- `itemCategories`: Categories for each type of item added by the mod.
  - `label`: The name of the category.
  - `tags`: Optional list of tags for the category. Inherited by all items.
  - `items`: A list of items in this category.
    - `name`: The name of the item.
    - `code`: The item's code (either in the mod description, or in the mod's `yaml` file).
    - `tags`: Optional tags for the item.

> NOTE: Browse the existing JSON files to see how the data is structured.

## Tagging

### Tag inheritance

Tags are meant to be inherited in two directions: 

1. From the mod to the items it adds.
2. From the items to the mod.

This means that the mod inherits all the tags of the items and item categories it adds, 
and the items inherit all the tags of the mod and their categories. This allows items
to have their own tags.

This makes it possible to refine searches by tag up to identifying individual items with 
specific tags. 

#### Example

A mod contains jewelry, shoes, and a dress. One of the shoes is holographic. 

The mod is tagged with:

- `Jewelry`, 
- `Feet`, 
- `FullBody` 
- `Holo` 

Searching for mods with the `Holo` tag will return this mod, even if only a single item 
is holographic. Searching for holographic items will return only the holographic shoe.

### Available Tags

#### Dependencies to other mods

- `AXL`: [ArchiveXL](https://www.nexusmods.com/cyberpunk2077/mods/4198)
- `EQEX`: [Equipment EX](https://www.nexusmods.com/cyberpunk2077/mods/6945)
- `TXL`: [TweakXL](https://www.nexusmods.com/cyberpunk2077/mods/4197)
- `CDW`: [Codeware](https://www.nexusmods.com/cyberpunk2077/mods/7780)
- `ATL`: [Virtual Atelier](https://www.nexusmods.com/cyberpunk2077/mods/2987)
- `R4EX`: [RED4Ext](https://www.nexusmods.com/cyberpunk2077/mods/2380)

#### Types of items

- `Clothing`
- `Head`
- `Hair`
- `Glasses`
- `Arms`
- `Hands`
- `Body`
- `Legs`
- `Feet`
- `Accessories`
- `Jewelry`
- `Emissive` - _Items that emit light_
- `Holo` - _Holographic items_

#### Gender support

- `FemV` - _Only for FemV characters_
- `MaleV` - _Only for MaleV characters_

#### Body mod support

- `Body-Vanilla` - _Standard game body_
- `Body-Hyst` - _[Enhanced Big Breasts Body](https://www.nexusmods.com/cyberpunk2077/mods/4654) aka EBB Body_
- `Body-Solo` - _[KS Solo Body](https://www.nexusmods.com/cyberpunk2077/mods/4813)_
- `Body-Lush` - _[Lush Body](https://www.nexusmods.com/cyberpunk2077/mods/4901)_
- `Body-Spawn0` - _[Spawn0 Body](https://www.nexusmods.com/cyberpunk2077/mods/1424)_
- `Body-VTK` - _[VTK Body](https://www.nexusmods.com/cyberpunk2077/mods/7054)_




[Cyberpunk 2077 Clothing Mods Database PHP]: https://github.com/Mistralys/cyberpunk-mod-db-php
[Aeon's Cyberpunk Mods]: https://aeonoftime.com/?article=2024-08-06-cyberpunk-clothing-mods&page=article