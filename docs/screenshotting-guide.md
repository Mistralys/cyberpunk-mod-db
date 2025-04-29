# Screenshotting

## The Philosophy

The mod screenshots are designed to be as standardized as possible to 
showcase the items the mods add. The goal is to provide a clear view of 
the items, and to make it easy to compare them.

> The screenshots are no replacement for the original mod screenshots, 
> which are always linked in the database.

## Prerequisites

1. **Save Games**: Use the save games from the [Mod DB sources](https://github.com/Mistralys/cyberpunk-mod-db-sources/blob/main/Save%20Games/savegames.md).
   There are instructions and requirements there to set up the game 
   for them.
2. **Screenshot size**: The mod screenshot images are designed to have 
   a size of 1920x1080. Ideally, the game's resolution for screenshots 
   should be higher - I have mine set at 2560x1440, which gives some leeway 
   for cropping, resizing and positioning.

## Shooting poses

### Step 1: Prepare the outfits

In the wardrobe, create and save the presets for the outfits you want 
to screenshot.

- Select outfits that represent most of the aspects of the mod's items
- Plan for three to four outfits per screenshot

> By saving presets, it's possible to switch between them in photo mode,
> the character staying in the exact same pose and position.

### Step 2: Set up the photo mode

- Use the AMM "Tools" menu to set the time to approximately **15:20**.
- Use one of the three pose slots stored in the save-game:
  1. Pose 1: Full body shot 
  2. Pose 2: Upper body shot (Tops, jackets, etc.)
  3. Pose 3: Lower body shot (Pants, shorts, skirts, etc.)
- Make sure that the floor line is level with the photo mode's dotted guidelines.
- Once everything is set, avoid rotating the camera with the right mouse button. 
  Instead, use the character's position sliders to adjust the pose.

### Step 3: Screenshot design

- Use up to four character positions max per screenshot
  - Personally, I like to shoot many to mix and match in Photoshop later.
- If relevant, think of adding a screenshot of the character's back.
- Use the [pose reference](./Poses/pose-reference.md) to find the best poses.
- If four positions are not enough to showcase the mod, shoot more: Multiple 
  screenshots are possible per mod.

### Step 4: Collate the screenshots

- Use the [PSD templates](https://github.com/Mistralys/cyberpunk-mod-db-sources/tree/main/Design) 
- Save the files as JPG
  - Quality 5 in the "Export" dialog
  - Quality 60 in the "Save for web" dialog

Naming schemes:

- `modname.jpg` - Main mod screenshot
- `modname-item-pants.jpg` - Item screenshot for the `pants` category ID
- `modname-male-v` - Custom screenshot, described in a separate JSON file

See [Adding multiple screenshots](#adding-multiple-screenshots) for more details
on custom screenshots.

## Typical command line workflow

1. Run `php cpmdb.php check-screens msg`
2. Copy a mod name to work on
3. Open the matching JSON data file
4. Run `php cpmdb.php mod="modname" cet` to get the codes
5. Load the save game, add the items
6. Take the screenshots
7. Create the mod screenshots / item screenshots
8. Run `php cpmdb.php mod="modname" check` to check the screenshots

## Useful poses

- **Gloves**: Zwei Fashion [#15 "Dressed to Kill"](./Poses/zwei-fashion/10-15.jpg)  
  This pose shows both hands and has the benefit of showing most of the texture. 
  Example: [Tactical Style Outfit Gloves](../data/clothing/screens/tactical-style-outfit-item-gloves.jpg)
- **Shoes**: Zwei Fashion [#14 "Force Manipulation"](./Poses/zwei-fashion/10-15.jpg)  
  Rotate it so the right shoe faces you directly, and the other to the side.
  Example: [Tactical Style Outfit Shoes](../data/clothing/screens/tactical-style-outfit-item-shoes.jpg)

## Adding multiple screenshots

Several screenshots can be added for a mod. In this case, a JSON sidecar file
is used to describe them and define the order in which they are displayed. This
file must be placed in the `data/clothing/screens` folder, and be named after
the mod ID: `mod-id.json`.

The screenshots can have freeform suffixes, which are used to identify them
in the sidecar file. Example:

- [blade-outfit-and-head.jpg](../data/clothing/screens/blade-outfit-and-head.jpg) - The main screenshot.
- [blade-outfit-and-head-male-v.jpg](../data/clothing/screens/blade-outfit-and-head-male-v.jpg) - Custom screenshot: The Male V variant.
- [blade-outfit-and-head-the-head.jpg](../data/clothing/screens/blade-outfit-and-head-the-head.jpg) - Custom screenshot: The head.
- [blade-outfit-and-head.json](../data/clothing/screens/blade-outfit-and-head.json) - Screenshot sidecar JSON file.

JSON Structure:

```json
{
  "male-v": {
    "title": "Male V variant",
    "tags": [
      "Male V"
    ]
  },
  "the-head": {
    "title": "Blade's head"
  }
}
```

As you can see, the suffixes are used as keys in the sidecar file.

> It is good practice to tag the MaleV - specific screenshots with the
> `MaleV` tag, as they will be recognized when using the command line
> tools to detect missing screenshots.
