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
require_once __DIR__.'/normalize-tags.php';

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
