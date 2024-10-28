# Using tags

## Introduction

Tags can be connected to mods, item categories and even individual items. 
They are used to quickly find items with specific properties.

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

### Dependencies to other mods

- `AXL`: [ArchiveXL](https://www.nexusmods.com/cyberpunk2077/mods/4198)
- `EQEX`: [Equipment EX](https://www.nexusmods.com/cyberpunk2077/mods/6945)
- `TXL`: [TweakXL](https://www.nexusmods.com/cyberpunk2077/mods/4197)
- `CDW`: [Codeware](https://www.nexusmods.com/cyberpunk2077/mods/7780)
- `ATL`: [Virtual Atelier](https://www.nexusmods.com/cyberpunk2077/mods/2987)
- `R4EX`: [RED4Ext](https://www.nexusmods.com/cyberpunk2077/mods/2380)

### Types of items

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

### Gender support

- `FemV` - _Only for FemV characters_
- `MaleV` - _Only for MaleV characters_

### Body mod support

- `Body-Vanilla` - _Standard game body_
- `Body-Hyst` - _[Enhanced Big Breasts Body](https://www.nexusmods.com/cyberpunk2077/mods/4654) aka EBB Body_
- `Body-Solo` - _[KS Solo Body](https://www.nexusmods.com/cyberpunk2077/mods/4813)_
- `Body-Lush` - _[Lush Body](https://www.nexusmods.com/cyberpunk2077/mods/4901)_
- `Body-Spawn0` - _[Spawn0 Body](https://www.nexusmods.com/cyberpunk2077/mods/1424)_
- `Body-VTK` - _[VTK Body](https://www.nexusmods.com/cyberpunk2077/mods/7054)_
