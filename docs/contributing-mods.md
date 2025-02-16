# Adding mods to the database

## Accepted mods

I am always open to add new mods to the database. Just keep in mind that this is 
a curated database: Not all mods will be accepted. I will review the mod and check 
if it fits the selection criteria. Some of these criteria include:

- Must be an apparel-related mod.
- Must be of reasonable quality.
- Should bring something new to the database.
- Should not be too skimpy: Some lingerie mods are OK, but are not the focus of the database.

## Essential helper mods

- [Cyber Engine Tweaks][]
- [Appearance Menu Mod][]
- [Character Customization Anywhere][]
- [Wardrobe Anywhere][]

## Step 1: Determining the mod ID

The original mod name is used as mod ID, lowercased and without any special characters. 

Example: "Alt's Necklaces" = `alts-necklaces`.

## Step 2: Creating the file

### Manually

At its most basic level, the JSON files can be created manually in a text
editor of your choice. They just have to follow the structure outlined in
the [JSON Structure Reference](#json-structure-reference).

Call the file `mod-id.json`. If you are making a pull request, make sure 
to place the file in the `data/clothing` folder.

> As I have experienced first-hand, creating these files manually becomes 
> tedious very quickly, especially when a mod contains many items. Use the
> command-line tools if you can.

### With the command-line tools

1. Run `php bin/cpmdb.php mod="mod-id" create` to create the JSON skeleton.
2. Open the created file `data/clothing/mod-id.json`.
3. Fill in the basic details, excluding items.

See the [command line tools reference](command-line-tools.md) for details. 

## Step 3: Adding the items

### Uncategorized vs categorized items

- **Uncategorized:**  
  When the items are of the same type (e.g., only pants),  
  they can be put directly into one default category. The category does
  not need to have label in this case. All items must have a category.
- **Categorized:**  
  When there are multiple types of items, or the mod contains
  different types of items (e.g., jackets and pants), they should be put 
  into separate categories.

### Finding the item codes

Typically, the CET codes are found in the following locations:

- A "Spoiler" section in the mod's description.
- A dedicated post in the "Articles" tab.
- A sticky comment.
- A txt file in the mod's archive file.

> NOTE: If none of these are available, and if the mod uses ArchiveXL,
> you can extract the codes from the mod's `.yaml` file.

### Adding item entries

At its most basic level, items are added manually to the `items` list
of a category, using the CET codes as specified in the mod's description.
Use the `add-category` command to append the skeleton for a new category
in your file.

**Determining item names:**

Item names can usually be derived from the CET codes, but if the codes are 
not descriptive, one reliable way is to check in-game. 

> When adding items using the CET console, they are added in the inventory 
> in the exact order of the commands. 

### Use GitHub Copilot to help

If you have access to GitHub Copilot, it can be a tremendous help for adding
the items.

1. Copy the CET codes for the target item category.
2. Paste the codes as-is under the `items` in the category.
3. Optional: It helps to sort the codes list alphabetically.
4. Fill out the first item entry to show Copilot what you want (name + code).
5. Press enter after the item entry
6. Let Copilot generate the rest of the items.

If the list of items is too long, Copilot will not generate all entries.
In this case, accept the generated entries, remove the item codes that
have been added (except the last one), and repeat the process.

> TIP: It helps to open a couple of existing JSON files, as Copilot will use
> their contents to more accurately detect what you want to do.

## Step 4: Adding tags

Correctly tagging the mods is crucial to be able to find relevant mods and
items. The tagging system is hierarchical:

- Mod-level tags: Must be shared by all item categories and items.
- Category-level tags: Must be shared by all items in the category.
- Item-level tags: Specific to the item.

Please refer to the [tagging reference](tagging-reference.md) for a list of
tags that can be used. 

## Step 5: Submitting JSON files

Create a pull request, or send the files to me via email at 
[eve@aeonoftime.com](mailto:eve@aeonoftime.com).

## Step 6: Review and feedback

I will review the mod and provide feedback if necessary. If everything is
in order, the mod will be added to the database. In the process, the JSON 
structure will be normalized (items sorted alphabetically, keys reordered, 
etc.). You don't have to worry about doing this on your end.

If you use tags that are not in the [tagging reference](tagging-reference.md),
I will consider adding or replacing them.

## Mod Screenshots

### The Philosophy

The mod screenshots are uniform to best showcase the items the mods add. 
The goal is to provide a clear view of the items, and to make it easy
to compare them.

The original mod pages are linked in the database, so it's easy to go there to
check out more screenshots and details.

### How to contribute screenshots

Right now screenshots are done solely by me. I plan to add a guide to make it
possible for contributors to add their own screenshots in the future. 

### Multiple screenshots

Several screenshots can be added for a mod. In this case, a JSON sidecar file
is used to describe them and define the order in which they are displayed. This
file must be placed in the `data/clothing/screens` folder, and be named after
the mod ID: `mod-id.json`.

The screenshots can have freeform suffixes, which are used to identify them
in the sidecar file. Example:

- `xrx-led-leotard.jpg` - The main screenshot.
- `xrx-led-leotard-nighttime.jpg` - The nighttime screenshot.
- `xrx-led-leotard.json` - The sidecar file.

JSON Structure:

```json
{
  "nighttime": {
    "title": "Lights as seen in the dark"
  }
}
```

As you can see, the suffixes are used as keys in the sidecar file. For the
moment, only the `title` key is used, but more keys may be added in the future
as the need arises.

## JSON Structure Reference

```json
{
  "mod": "Mod Name",
  "url": "https://www.nexusmods.com/cyberpunk2077/mods/1234",
  "atelier": "https://www.nexusmods.com/cyberpunk2077/mods/1234",
  "atelierName": "Someone's Awesome Store",
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
  "linkedMods": [
    "mod-one-id",
    "mod-two-id"
  ],
  "seeAlso": [
    {
      "url": "https://link-to-page",
      "label": "Optional link label"
    }
  ],
  "searchTweaks": "space separated words",
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
- `atelierName`: The original name of the Atelier mod (if any).
- `authors`: A list of mod author names.
- `tags`: A list of tags identifying dependencies to other mods and the kind of items.
- `comments`: Optional comments about the mod.
- `linkedMods`: Optional list of mod IDs that are related to this mod.
- `seeAlso`: Optional list of links to related mods or pages.
    - `url`: The URL to the related page.
    - `label`: Optional label for the link.
- `searchTweaks`: Optional space-separated list of words to help prefix search engines like [MeiliSearch](https://www.meilisearch.com/) or [Loupe](https://github.com/loupe-php/loupe). These engines only search at the beginning of words, so searching for "flower" will not find "sunflower". Adding "flower" to the search tweaks makes it possible to fix this issue.
- `itemCategories`: Categories for each type of item added by the mod.
    - `label`: The name of the category.
    - `tags`: Optional: Tags specific to the category. Inherited by all items.
    - `items`: A list of items in this category.
        - `name`: The name of the item.
        - `code`: The item's code (either in the mod description, or in the mod's `yaml` file).
        - `tags`: Optional: Tags specific to the item.

See the [tagging reference](tagging-reference.md) for more information on how to use
tags, and which ones can be used.

> NOTE: Browse the existing JSON files to see how the data is structured.


[Cyber Engine Tweaks]: https://www.nexusmods.com/cyberpunk2077/mods/107
[Appearance Menu Mod]: https://www.nexusmods.com/cyberpunk2077/mods/790
[Wardrobe Anywhere]: https://www.nexusmods.com/cyberpunk2077/mods/5145
[Character Customization Anywhere]: https://www.nexusmods.com/cyberpunk2077/mods/3930
