<?php
/**
 * Script used to normalize the JSON files, by doing
 * the following:
 *
 * - Ensuring the keys are all in the same order
 * - Sorting relevant elements alphabetically
 * - Removing empty categories
 * - Adding missing tags
 *
 * The output files are stored in the subfolder `normalized`
 * to be able to review them before replacing the original files.
 *
 * @package CPMDB
 */

declare(strict_types=1);

namespace CPMDB\Assets;

require_once __DIR__.'/prepend.php';

use AppUtils\FileHelper\JSONFile;

/**
 * @return string|null
 */
function getNormalizeArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['normalize'] ??
        $commands['norm'] ??
        $commands['nm'] ??
        null;
}

function normalizeFile(JSONFile $file) : void
{
    logHeader('Data file [%s] - Normalizing structure', $file->getName());

    $data = $file->parse();
    $converted = array();

    foreach(KEYS_ORDER as $key => $value) {
        $converted[$key] = $data[$key] ?? $value;
    }

    if(empty($converted['comments'])) {
        unset($converted['comments']);
    }

    if(empty($converted['linkedMods'])) {
        unset($converted['linkedMods']);
    } else {
        sort($converted['linkedMods']);
    }

    if(empty($converted['seeAlso'])) {
        unset($converted['seeAlso']);
    } else {
        usort($converted['seeAlso'], function(array $a, array $b) : int {
            $labelA = $a['label'] ?? '';
            $urlA = $a['url'] ?? '';
            $labelB = $b['label'] ?? '';
            $urlB = $b['url'] ?? '';

            return strnatcasecmp($labelA.$urlA, $labelB.$urlB);
        });
    }

    $converted['tags'] = normalizeTags($converted['tags']);

    sort($converted['authors']);

    $categories = $data['itemCategories'];

    // Sort categories by label
    usort($categories, function(array $a, array$b) : int {
        return strnatcasecmp($a['label'], $b['label']);
    });

    $keep = array();
    foreach($categories as $category)
    {
        // Prune empty categories
        if(empty($category['items'])) {
            continue;
        }

        // Add tags if not present
        if(!isset($category['tags'])) {
            $category['tags'] = array();
        }

        $category['tags'] = normalizeTags($category['tags']);

        foreach($category['items'] as $idx => $item) {
            $normalizedItem = array(
                'name' => $item['label'] ?? $item['name'] ?? '',
                'code' => $item['code'] ?? ''
            );

            if(!empty($item['tags'])) {
                $normalizedItem['tags'] = normalizeTags($item['tags']);
            }

            $category['items'][$idx] = $normalizedItem;
        }

        // Sort items by name
        usort($category['items'], function(array $a, array$b) : int {
            return strnatcasecmp($a['name'], $b['name']);
        });

        $keep[] = $category;
    }

    $converted['itemCategories'] = $keep;

    JSONFile::factory($file->getFolderPath().'/normalized/'. $file->getName())
        ->setEscapeSlashes(false)
        ->setTrailingNewline(true)
        ->putData($converted, true);

    logInfo('File normalized successfully.');
    logEmptyLine();
}

const KEYS_ORDER = array(
    'mod' => '',
    'url' => '',
    'atelier' => '',
    'authors' => array(),
    'tags' => array(),
    'linkedMods' => array(),
    'seeAlso' => array(),
    'comments' => '',
    'itemCategories' => array()
);

const TAGS_NORMALIZED = array(
    'accessories' => 'Accessories',
    'accessory' => 'Accessories',
    'animated' => 'Animated',
    'animation' => 'Animated',
    'arm' => 'Arms',
    'arms' => 'Arms',
    'autoscale' => 'AutoScale',
    'axl' => 'AXL',
    'belt' => 'Belt',
    'belts' => 'Belt',
    'body' => 'FullBody',
    'body-adonis' => 'Body-Adonis',
    'body-angel' => 'Body-Hyst-Angel',
    'body-evb' => 'Body-EVB',
    'body-gymfiend' => 'Body-Gymfiend',
    'body-ebb' => 'Body-Hyst-EBB',
    'body-rb' => 'Body-Hyst-RB',
    'body-ebbp' => 'Body-Hyst-EBBP',
    'body-ebbprb' => 'Body-Hyst-EBBPRB',
    'body-ebbrb' => 'Body-Hyst-EBBRB',
    'body-hyst' => 'Body-Hyst',
    'body-hyst-angel' => 'Body-Hyst-Angel',
    'body-hyst-ebb' => 'Body-Hyst-EBB',
    'body-hyst-ebb-bb' => 'Body-Hyst-EBBRB',
    'body-hyst-ebb-rb' => 'Body-Hyst-EBBRB',
    'body-hyst-ebbp' => 'Body-Hyst-EBBP',
    'body-hyst-ebbprb' => 'Body-Hyst-EBBPRB',
    'body-hyst-ebbrb' => 'Body-Hyst-EBBRB',
    'body-hyst-rb' => 'Body-Hyst-RB',
    'body-lush' => 'Body-Lush',
    'body-small' => 'Body-Small',
    'body-solo' => 'Body-Solo',
    'body-solush' => 'Body-Solo-Lush',
    'body-atlas' => 'Body-Atlas',
    'body-solo-arms' => 'Body-Solo-Arms',
    'body-solo-small' => 'Body-Solo-Small',
    'body-solo-ultimate' => 'Body-Solo-Ultimate',
    'body-soloarms' => 'Body-Solo-Arms',
    'body-spawn0' => 'Body-Spawn0',
    'body-suit' => 'Bodysuit',
    'body-valentine' => 'Body-Valentine',
    'body-vanilla' => 'Body-Vanilla',
    'body-vanillahd' => 'Body-VTK-VanillaHD',
    'body-vtk' => 'Body-VTK',
    'body-vtk-vanilla-hd' => 'Body-VTK-VanillaHD',
    'body-vtk-vanillahd' => 'Body-VTK-VanillaHD',
    'bodysuit' => 'Bodysuit',
    'bodysuits' => 'Bodysuit',
    'boot' => 'Boots',
    'boots' => 'Boots',
    'bra' => 'Bra',
    'bras' => 'Bra',
    'cdw' => 'CDW',
    'choker' => 'Choker',
    'chokers' => 'Choker',
    'clothing' => 'Clothing',
    'coat' => 'Coat',
    'coats' => 'Coat',
    'collar' => 'Choker',
    'diy' => 'DIY',
    'dress' => 'Dress',
    'dresses' => 'Dress',
    'earring' => 'Earring',
    'earrings' => 'Earring',
    'ears' => 'Head',
    'emissive' => 'Emissive',
    'eqex' => 'EQEX',
    'face' => 'Head',
    'feet' => 'Feet',
    'femv' => 'FemV',
    'foot' => 'Feet',
    'full body' => 'FullBody',
    'full-body' => 'FullBody',
    'fullbody' => 'FullBody',
    'glasses' => 'Glasses',
    'glove' => 'Gloves',
    'gloves' => 'Gloves',
    'glow' => 'Emissive',
    'hair' => 'Hair',
    'hand' => 'Hands',
    'hands' => 'Hands',
    'hat' => 'Hat',
    'head' => 'Head',
    'holo' => 'Holo',
    'hologram' => 'Holo',
    'holograms' => 'Holo',
    'holographic' => 'Holo',
    'jacket' => 'Jacket',
    'jackets' => 'Jacket',
    'jewellery' => 'Jewelry',
    'jewelry' => 'Jewelry',
    'leg' => 'Legs',
    'leggings' => 'Leggings',
    'legs' => 'Legs',
    'malev' => 'MaleV',
    'mask' => 'Mask',
    'masks' => 'Mask',
    'modular' => 'Modular',
    'navel' => 'Navel',
    'neck' => 'Neck',
    'necklace' => 'Necklace',
    'necklaces' => 'Necklace',
    'nsfw' => 'NSFW',
    'outfit' => 'Outfit',
    'outfits' => 'Outfit',
    'panties' => 'Panties',
    'pants' => 'Pants',
    'panty' => 'Panties',
    'physics' => 'Physics',
    'piercing' => 'Piercing',
    'piercings' => 'Piercing',
    'ring' => 'Ring',
    'rings' => 'Ring',
    'shirt' => 'Shirt',
    'shirts' => 'Shirt',
    'shoe' => 'Shoes',
    'shoes' => 'Shoes',
    'shorts' => 'Shorts',
    'skimpy' => 'Skimpy',
    'skirt' => 'Skirt',
    'skirts' => 'Skirt',
    'sleeves' => 'Sleeves',
    'stockings' => 'Stockings',
    'suit' => 'Suit',
    'suits' => 'Suit',
    't-shirt' => 'Shirt',
    't-shirts' => 'Shirt',
    'top' => 'Top',
    'tops' => 'Top',
    'torso' => 'Torso',
    'transparent' => 'Transparent',
    'tshirt' => 'Shirt',
    'tshirts' => 'Shirt',
    'txl' => 'TXL',
    'waist' => 'Waist',
);

function normalizeTags(array $tags) : array
{
    $normalized = array();

    foreach($tags as $tag)
    {
        $tag = strtolower($tag);
        if(!isset(TAGS_NORMALIZED[$tag])) {
            logError('Unknown tag: '.$tag);
            continue;
        }

        $normalized[] = TAGS_NORMALIZED[$tag];
    }

    sort($normalized);

    return $normalized;
}

function normalizeAll() : void
{
    foreach(getFiles() as $file) {
        normalizeFile($file);
    }
}
