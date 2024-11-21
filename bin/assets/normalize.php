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

use AppUtils\ConvertHelper;
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

/**
 * @return string|null
 */
function getNormalizeAteliersArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['normalize-ateliers'] ??
        $commands['normalizeateliers'] ??
        $commands['norm-ateliers'] ??
        $commands['normateliers'] ??
        $commands['nmat'] ??
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

    if(empty($converted[KEY_COMMENTS])) {
        unset($converted[KEY_COMMENTS]);
    }

    if(empty($converted[KEY_LINKED_MODS])) {
        unset($converted[KEY_LINKED_MODS]);
    } else {
        sort($converted[KEY_LINKED_MODS]);
    }

    if(empty($converted[KEY_SEE_ALSO])) {
        unset($converted[KEY_SEE_ALSO]);
    } else {
        usort($converted[KEY_SEE_ALSO], function(array $a, array $b) : int {
            $labelA = $a[KEY_SEE_ALSO_LABEL] ?? '';
            $urlA = $a[KEY_SEE_ALSO_URL] ?? '';
            $labelB = $b[KEY_SEE_ALSO_LABEL] ?? '';
            $urlB = $b[KEY_SEE_ALSO_URL] ?? '';

            return strnatcasecmp($labelA.$urlA, $labelB.$urlB);
        });
    }

    $converted[KEY_TAGS] = normalizeTags($converted[KEY_TAGS]);

    sort($converted[KEY_AUTHORS]);

    if(!empty($converted[KEY_ATELIER])) {
        $converted[KEY_ATELIER_NAME] = getAtelierName($converted[KEY_ATELIER]);
    }

    $categories = $data[KEY_ITEM_CATEGORIES];

    // Sort categories by label
    usort($categories, function(array $a, array$b) : int {
        return strnatcasecmp($a[KEY_CAT_LABEL], $b[KEY_CAT_LABEL]);
    });

    $keep = array();
    foreach($categories as $category)
    {
        // Prune empty categories
        if(empty($category[KEY_CAT_ITEMS])) {
            continue;
        }

        // Add tags if not present
        if(!isset($category[KEY_CAT_TAGS])) {
            $category[KEY_CAT_TAGS] = array();
        }

        $category[KEY_CAT_TAGS] = normalizeTags($category[KEY_CAT_TAGS]);

        foreach($category[KEY_CAT_ITEMS] as $idx => $item) {
            $normalizedItem = array(
                KEY_ITEM_NAME => $item['label'] ?? $item[KEY_ITEM_NAME] ?? '',
                KEY_ITEM_CODE => $item[KEY_ITEM_CODE] ?? ''
            );

            if(!empty($item[KEY_ITEM_TAGS])) {
                $normalizedItem[KEY_ITEM_TAGS] = normalizeTags($item[KEY_ITEM_TAGS]);
            }

            $category[KEY_CAT_ITEMS][$idx] = $normalizedItem;
        }

        // Sort items by name
        usort($category[KEY_CAT_ITEMS], function(array $a, array$b) : int {
            return strnatcasecmp($a[KEY_ITEM_NAME], $b[KEY_ITEM_NAME]);
        });

        $keep[] = $category;
    }

    $converted[KEY_ITEM_CATEGORIES] = $keep;

    $converted[KEY_SEARCH_TERMS] = resolveSearchTerms($converted);

    if(empty($converted[KEY_SEARCH_TERMS])) {
        unset($converted[KEY_SEARCH_TERMS]);
    }

    $file
        ->setEscapeSlashes(false)
        ->setTrailingNewline(true)
        ->putData($converted, true);

    logInfo('File normalized successfully.');
    logEmptyLine();
}

function resolveSearchTerms(array $modData) : string
{
    $contents =
        implode(' ', $modData[KEY_AUTHORS]).' '.
        $modData[KEY_MOD].' '.
        $modData[KEY_ATELIER_NAME].' ';

    $existing = str_replace(',', ' ', $modData[KEY_SEARCH_TERMS] ?? '');
    $terms = ConvertHelper::explodeTrim(' ', $existing);

    foreach(SEARCH_TERMS as $search => $replace) {
        if(stripos($contents, $search) !== false) {
            array_push($terms, ...ConvertHelper::explodeTrim(' ', $replace));
        }
    }

    $terms = array_unique($terms);

    sort($terms);

    return implode(' ', $terms);
}

function getAtelierName(string $atelierURL) : string
{
    foreach(getAteliers() as $atelier) {
        if($atelier['url'] === $atelierURL) {
            return $atelier['name'];
        }
    }

    logError('Unknown atelier URL ['.$atelierURL.'].');

    return '';
}

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
    if(isset($GLOBALS['__tags'])) {
        return $GLOBALS['__tags'];
    }

    $tags = JSONFile::factory(__DIR__.'/../../data/tags.json')->getData();
    ksort($tags);

    $GLOBALS['__tags'] = $tags;

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
        $aliases = $tagDef[KEY_TAGS_ALIASES] ?? array();
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
        $category = $tagDef[KEY_TAGS_CATEGORY] ?? 'Miscellaneous';
        $categorized[$category][$tagName] = $tagDef;
    }

    uksort($categorized, 'strnatcasecmp');

    return $categorized;
}

function normalizeAteliers() : void
{
    $ateliers = getAteliers();

    logHeader('Normalizing ateliers');
    logInfo('Found [%s] ateliers.', count($ateliers));

    foreach($ateliers as $id => $data) {
        $ateliers[$id] = normalizeAtelier($data);
    }

    getAteliersFile()->putData($ateliers);

    logInfo('Ateliers normalized successfully.');
}

function normalizeAtelier(array $data) : array
{
    sort($data[KEY_ATELIERS_AUTHORS]);

    return $data;
}