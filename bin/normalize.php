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

use AppUtils\FileHelper;
use AppUtils\FileHelper\JSONFile;

require_once __DIR__ . '/../vendor/autoload.php';

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

        // Sort items by name
        usort($category['items'], function(array $a, array$b) : int {
            return strnatcasecmp($a['name'], $b['name']);
        });

        // Sort the item tags
        foreach($category['items'] as $idx => $item) {
            if(isset($item['tags'])) {
                $category['items'][$idx]['tags'] = normalizeTags($item['tags']);
            }
        }

        $keep[] = $category;
    }

    $converted['itemCategories'] = $keep;

    $newFile = JSONFile::factory($file->getFolderPath().'/normalized/'. $file->getName())->putData($converted, true);

    $json = file_get_contents((string)$newFile);
    $json = str_replace('\/', '/', $json);
    $json .= PHP_EOL;

    file_put_contents((string)$newFile, $json);
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
    'leg' => 'Legs',
    'legs' => 'Legs',
    'full-body' => 'FullBody',
    'fullbody' => 'FullBody',
    'top' => 'Top',
    'tops' => 'Top',
    'torso' => 'Torso',
    'panties' => 'Panties',
    'panty' => 'Panties',
    'bra' => 'Bra',
    'bras' => 'Bra',
    'navel' => 'Navel',
    'waist' => 'Waist',
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
    'shorts' => 'Shorts',
    'pants' => 'Pants',
    'body' => 'FullBody',
    'leggings' => 'Leggings',
    'skimpy' => 'Skimpy',
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
    'body-solo' => 'Body-Solo',
    'body-lush' => 'Body-Lush',
    'body-spawn0' => 'Body-Spawn0',
    'body-adonis' => 'Body-Adonis',
    'body-gymfiend' => 'Body-Gymfiend',
    'body-vtk' => 'Body-VTK',
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

function logInfo(string $message, ...$args) : void
{
    echo sprintf($message, ...$args).PHP_EOL;
}

function logError(string $message, ...$args) : void
{
    echo '...! '.sprintf($message, ...$args).PHP_EOL;
}

$files = FileHelper::createFileFinder(__DIR__.'/../data/clothing')
    ->includeExtension('json')
    ->getFileInfos();

foreach($files as $file) {
    if ($file instanceof JSONFile) {
        normalizeFile($file);
    }
}
