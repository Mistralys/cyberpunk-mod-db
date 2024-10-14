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

require_once __DIR__.'/prepend.php';

use AppUtils\FileHelper\JSONFile;

function normalizeFile(JSONFile $file) : void
{
    logInfo('Normalizing file [%s]...', $file->getName());

    $data = $file->parse();
    $converted = array();

    foreach(KEYS_ORDER as $key => $value) {
        $converted[$key] = $data[$key] ?? $value;
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
}

const KEYS_ORDER = array(
    'mod' => '',
    'url' => '',
    'atelier' => '',
    'authors' => array(),
    'tags' => array(),
    'itemCategories' => array()
);

const TAGS_NORMALIZED = array(
    'clothing' => 'Clothing',
    'femv' => 'FemV',
    'malev' => 'MaleV',
    'dress' => 'Dress',
    'dresses' => 'Dress',
    'glove' => 'Gloves',
    'gloves' => 'Gloves',
    'hand' => 'Hands',
    'hands' => 'Hands',
    'jackets' => 'Jacket',
    'jacket' => 'Jacket',
    'suit' => 'Suit',
    'stockings' => 'Stockings',
    'sleeves' => 'Sleeves',
    'leg' => 'Legs',
    'legs' => 'Legs',
    'full-body' => 'FullBody',
    'fullbody' => 'FullBody',
    'full body' => 'FullBody',
    'top' => 'Top',
    'tops' => 'Top',
    'torso' => 'Torso',
    'panties' => 'Panties',
    'panty' => 'Panties',
    'bra' => 'Bra',
    'bras' => 'Bra',
    'navel' => 'Navel',
    'waist' => 'Waist',
    'hat' => 'Hat',
    'neck' => 'Neck',
    'necklace' => 'Necklace',
    'necklaces' => 'Necklace',
    'head' => 'Head',
    'face' => 'Head',
    'glasses' => 'Glasses',
    'jewelry' => 'Jewelry',
    'jewellery' => 'Jewelry',
    'ears' => 'Head',
    'mask' => 'Mask',
    'masks' => 'Mask',
    'accessory' => 'Accessories',
    'accessories' => 'Accessories',
    'boot' => 'Boots',
    'boots' => 'Boots',
    'feet' => 'Feet',
    'foot' => 'Feet',
    'shoes' => 'Shoes',
    'shoe' => 'Shoes',
    'piercing' => 'Piercing',
    'piercings' => 'Piercing',
    'modular' => 'Modular',
    'outfit' => 'Outfit',
    'outfits' => 'Outfit',
    'chokers' => 'Choker',
    'choker' => 'Choker',
    'collar' => 'Choker',
    'bodysuit' => 'Bodysuit',
    'bodysuits' => 'Bodysuit',
    'body-suit' => 'Bodysuit',
    'belt' => 'Belt',
    'belts' => 'Belt',
    'physics' => 'Physics',
    'shirt' => 'Shirt',
    'shirts' => 'Shirt',
    't-shirt' => 'Shirt',
    't-shirts' => 'Shirt',
    'tshirt' => 'Shirt',
    'tshirts' => 'Shirt',
    'emissive' => 'Emissive',
    'glow' => 'Emissive',
    'shorts' => 'Shorts',
    'pants' => 'Pants',
    'body' => 'FullBody',
    'leggings' => 'Leggings',
    'skimpy' => 'Skimpy',
    'diy' => 'DIY',
    'nsfw' => 'NSFW',
    'coat' => 'Coat',
    'coats' => 'Coat',
    'holo' => 'Holo',
    'holographic' => 'Holo',
    'hologram' => 'Holo',
    'holograms' => 'Holo',
    'transparent' => 'Transparent',
    'skirts' => 'Skirt',
    'skirt' => 'Skirt',
    'earring' => 'Earring',
    'earrings' => 'Earring',
    'animated' => 'Animated',
    'animation' => 'Animated',
    'body-small' => 'Body-Small',
    'body-hyst' => 'Body-Hyst',
    'body-hyst-ebb' => 'Body-Hyst-EBB',
    'body-hyst-ebb-bb' => 'Body-Hyst-EBBRB',
    'body-hyst-ebb-rb' => 'Body-Hyst-EBBRB',
    'body-hyst-ebbp' => 'Body-Hyst-EBBP',
    'body-hyst-ebbrb' => 'Body-Hyst-EBBRB',
    'body-hyst-ebbprb' => 'Body-Hyst-EBBPRB',
    'body-hyst-rb' => 'Body-Hyst-RB',
    'body-hyst-angel' => 'Body-Hyst-Angel',
    'body-angel' => 'Body-Hyst-Angel',
    'body-vanilla' => 'Body-Vanilla',
    'body-soloarms' => 'Body-Solo-Arms',
    'body-solo-arms' => 'Body-Solo-Arms',
    'body-solo' => 'Body-Solo',
    'body-lush' => 'Body-Lush',
    'body-spawn0' => 'Body-Spawn0',
    'body-adonis' => 'Body-Adonis',
    'body-gymfiend' => 'Body-Gymfiend',
    'body-vtk' => 'Body-VTK',
    'body-vtk-vanilla-hd' => 'Body-VTK-VanillaHD',
    'body-vtk-vanillahd' => 'Body-VTK-VanillaHD',
    'body-vanillahd' => 'Body-VTK-VanillaHD',
    'body-evb' => 'Body-EVB',
    'axl' => 'AXL',
    'eqex' => 'EQEX',
    'cdw' => 'CDW',
    'txl' => 'TXL',
    'arm' => 'Arms',
    'arms' => 'Arms',
    'ring' => 'Ring',
    'rings' => 'Ring',
    'hair' => 'Hair',
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
