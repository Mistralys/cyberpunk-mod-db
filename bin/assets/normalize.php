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

use AppUtils\FileHelper\JSONFile;
use AppUtils\FileHelper_Exception;

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

/**
 * @return string|null
 */
function getNormalizeAllArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['normalize-all'] ??
        $commands['normalizeall'] ??
        $commands['norm-all'] ??
        $commands['normall'] ??
        $commands['nma'] ??
        null;
}

function normalizeFile(JSONFile $file) : void
{
    logHeader('Data file [%s] - Normalizing structure', $file->getName());

    $data = $file->parse();

    if(!empty($data['relatedMods'])) {
        $data['linkedMods'] = $data['relatedMods'];
        unset($data['relatedMods']);
    }

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

    $file
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

/**
 * @param string[] $tags
 * @return string[]
 * @throws FileHelper_Exception
 */
function normalizeTags(array $tags) : array
{
    $normalized = array();
    $tagAliases = getTagAliases();

    foreach($tags as $tag)
    {
        $tag = strtolower($tag);
        if(!isset($tagAliases[$tag])) {
            logError('Unknown tag ['.$tag.']');
            continue;
        }

        $normalized[] = $tagAliases[$tag];
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

/**
 * Loads all tags from the JSON file.
 *
 * @return array<string,array{description:string,links:array<int,array{url:string,label:string}>|NULL,aliases:array<int,string>|NULL}>
 * @throws FileHelper_Exception
 */
function getTags() : array
{
    $tags = JSONFile::factory(__DIR__.'/../../data/tags.json')->getData();
    ksort($tags);

    return $tags;
}

/**
 * @return array<string,string>
 * @throws FileHelper_Exception
 */
function getTagAliases() : array
{
    $result = array();

    foreach(getTags() as $tagName => $tagDef) {
        $aliases = $tagDef['aliases'] ?? array();
        $aliases[] = strtolower($tagName);
        foreach($aliases as $alias) {
            $result[$alias] = $tagName;
        }
    }

    ksort($result);

    return $result;
}

function getTagsCategorized() : array
{
    $categorized = array();

    foreach(getTags() as $tagName => $tagDef) {
        $category = $tagDef['category'] ?? 'Miscellaneous';
        $categorized[$category][$tagName] = $tagDef;
    }

    uksort($categorized, 'strnatcasecmp');

    return $categorized;
}
