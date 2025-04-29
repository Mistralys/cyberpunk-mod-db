# Using tags

## Introduction

Tags can be connected to mods, item categories and even individual items. 
They are used to quickly find items with specific properties. They are
available as a separate JSON file, [tags.json](../data/tags.json).

## Tag inheritance

Tags are meant to be inherited in two directions:

1. From the mod to the items it adds.
2. From the items to the mod.

This means that the mod inherits all the tags of the items and item categories it adds,
and the items inherit all the tags of the mod and their categories. This allows items
to have their own tags.

This makes it possible to refine searches by tag up to identifying individual items with
specific tags.

### Example

A mod contains jewelry, shoes, and a dress. One of the shoes is holographic.

The mod is tagged with:

- `Jewelry`,
- `Feet`,
- `FullBody`
- `Holo`

Searching for mods with the `Holo` tag will return this mod, even if only a single item
is holographic. Searching for holographic items will return only the holographic shoe.

## Available Tags reference

### Clothing items

- `Accessories` - Clothing accessories
- `Belt`
- `Bodysuit`
- `Boots`
- `Bra` - Bra or bra-like
- `Bracelet`
- `Choker` - Neck choker
- `Coat`
- `Corset`
- `Decals` - Decals or stickers to use on clothing items
- `Dress`
- `Earring`
- `Glasses`
- `Gloves`
- `Hat`
- `Helmet`
- `Jacket` - Jacket, Blazer, Bolero
- `Jewelry`
- `Leggings` - Leggings, stretch pants
- `Lingerie`
- `Mask`
- `Necklace`
- `Panties`
- `Pants`
- `Piercing`
- `Ring`
- `Shirt`
- `Shoes`
- `Shorts`
- `Skirt`
- `Sleeves`
- `Stockings`
- `Suit`
- `Top`
- `Underwear`

### Clothing slots

- `Arms` - Arms slot
- `Feet` - Feet slot
- `FullBody` - Full body clothing items
- `Hair` - Hair slot
- `Hands` - Hand slot
- `Head` - Head slot
- `Legs` - Leg slot
- `Navel` - Navel slot
- `Neck` - Neck slot
- `Torso` - Torso slot
- `Waist` - Waist slot

### Item properties

- `Animated` - Animated clothing parts
- `AutoScale` - Clothing that automatically scales to the player's level
- `Body-Vanilla` - The vanilla body for FemV and MaleV
- `Clothing` - Clothing items
- `Cosplay` - Clothing items that are inspired by characters from other media
- `DIY` - "Do-It-Yourself" - Clothing items that can be customized with Wolvenkit
- `Emissive` - Clothing items that glow or emit light in some way
- `FemV` - Items restricted to female V characters
- `GarmentSupport` - Clothing system that handles tucking pants into boots and shirts under jackets. [Modding Wiki article](https://wiki.redmodding.org/cyberpunk-2077-modding/for-mod-creators-theory/3d-modelling/garment-support-how-does-it-work)
- `Holo` - Holographic items
- `MaleV` - Items restricted to male V characters
- `Modular` - Modular clothing items that can be combined in different ways
- `Outfit` - Set of clothing items for a full-themed outfit
- `Physics` - Items with working physics
- `Reflective` - Items with reflective surfaces
- `Skimpy` - Clothing items that are revealing
- `Transparent` - Items with transparency

### Mods - Body Mods - FemV

- `Body-EVB` - Enhanced Vanilla Body; No refits needed [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/11489)
- `Body-Elegy` - Body with improved proportions, successor to Project Valentine. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/19821) [Project Valentine](https://www.nexusmods.com/cyberpunk2077/mods/4256)
- `Body-Flat` - Flat chested body [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/6883)
- `Body-Hyst` - Enhanced Big Breasts, Realistic Butt, and more [Nexus](https://next.nexusmods.com/profile/LxRHyst/mods?gameId=3333)
- `Body-Hyst-Angel` - New improved version of the other Hyst body mods [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/14896)
- `Body-Hyst-EBB` - EBB - Enhanced Big Breasts [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4654)
- `Body-Hyst-EBBN` - Enhanced Big Breasts Natural [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4654)
- `Body-Hyst-EBBNRB` - Enhanced Big Breasts Natural and Realistic Butt [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4654)
- `Body-Hyst-EBBP` - EBBP - Enhanced Big Breasts and Push-Up [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/9083)
- `Body-Hyst-EBBPRB` - EBBPRB - Enhanced Big Breasts, Push-Up and Realistic Butt [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/9083)
- `Body-Hyst-EBBRB` - EBBRB - Enhanced Big Breasts and Realistic Butt [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4654)
- `Body-Hyst-RB` - Realistic Butt body [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4420)
- `Body-Lush` - Voluptuous body and large breasts [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4901)
- `Body-SoLush` - Lush body with more muscles [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/8392)
- `Body-Solo` - Rock-hard abs and toned body [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4813)
- `Body-Solo-Arms` - Athletic arms [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/7148)
- `Body-Solo-Small` - Solo with small breasts [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/6213)
- `Body-Solo-Ultimate` - Solo with bigger breasts and butt [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/6944)
- `Body-Songbird` - Songbird's body for the FemV player character [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/12898)
- `Body-Spawn0` - Body with a large choice of sizes and CET in-game body swap [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/1424)
- `Body-VTK-Big` - Vanilla HD body with bigger breasts [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/7482)
- `Body-VTK-Small` - Vanilla HD body with small breasts [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/7482)
- `Body-VTK-VanillaHD` - High quality body, requires no refits [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/7054)
- `Body-Valentine` - Body with improved proportions [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4256)

### Mods - Body Mods - MaleV

- `Body-Adonis` - Adonis: High-poly body with detailed musculature and definition [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4968)
- `Body-Atlas` - High poly body with hand-sculpted muscles [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/8766)
- `Body-Gymfiend` - Muscular body [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/6423)

### Mods - Modder Resource

- `AXL` - "ArchiveXL" - Archive extender used to load custom resources without touching original game files. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4198)
- `CDW` - "Codeware" - Library and framework for creating RedScript and Cyber Engine Tweaks mods. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/7780)
- `CPAL` - "Community Palette Project" - Adds 730* color options to a wide variety of materials in the game. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/10437)
- `MIBL` - "Microblend Resource" - Collection of custom microblends that can be used when recoloring objects. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4571)
- `MatEX` - "Multilayer Material Extender" - Pure black and white colors for mods to use. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/5570)
- `R4EX` - "Red4Ext" - A script extender for REDengine 4. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/2380)
- `TXL` - "Tweak XL" - TweakDB modifications loader and script extensions [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/4197)

### Mods - Tools

- `ACM` - "Appearance Creator Mod" - Change NPC clothing in-game without any restart. Export as AMM Appearance and share your created outfit. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/10795)
- `CET` - "Cyber Engine Tweaks" - Scripting framework for modders and quality of life fixes. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/107)
- `EQEX` - "Equipment-EX" - Layering of clothing items [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/6945)
- `VAT` - "Virtual Atelier" - Adds clothing stores to the in-game browser. [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/2987)

### Mods - Utilities

- `BodyTags` - Vanilla small and big body tags [Nexus](https://www.nexusmods.com/cyberpunk2077/mods/19286)

### Utilities

- `Toggles` - Toggles for clothing parts
