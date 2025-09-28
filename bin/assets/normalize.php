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

const GLOBAL_TAGS = '__cpmdb_tags';

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

/**
 * @return string|null
 */
function getNormalizeTagsArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['normalize-tags'] ??
        $commands['normalizetags'] ??
        $commands['norm-tags'] ??
        $commands['normtags'] ??
        $commands['ntags'] ??
        null;
}

function normalizeFile(JSONFile $file) : void
{
    logHeader('Data file [%s] - Normalizing structure', $file->getName());

    $modID = $file->getBaseName();
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

    if(empty($converted[KEY_MOD_VERSION])) {
        unset($converted[KEY_MOD_VERSION]);
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

    $converted[KEY_TAGS] = normalizeTags($converted[KEY_TAGS], 'mod ['.$modID.']');

    $allTags = $converted[KEY_TAGS];

    sort($converted[KEY_AUTHORS]);

    if(!empty($converted[KEY_ATELIER])) {
        $converted[KEY_ATELIER_NAME] = getAtelierName($converted[KEY_ATELIER], 'mod ['.$modID.']');
    }

    $converted[KEY_ITEM_CATEGORIES] = normalizeCategories($modID, $data[KEY_ITEM_CATEGORIES] ?? array(), $allTags);

    $converted[KEY_SEARCH_TERMS] = resolveSearchTerms($converted);

    if(empty($converted[KEY_SEARCH_TERMS])) {
        unset($converted[KEY_SEARCH_TERMS]);
    }

    analyzeTagRecommendations($allTags, $modID);

    $file
        ->setEscapeSlashes(false)
        ->setTrailingNewline(true)
        ->putData($converted, true);

    logInfo('File normalized successfully.');
    logEmptyLine();
}

/**
 * @param string $modID
 * @param array<int,mixed> $categories
 * @param string[] $allTags
 * @return array<int,mixed>
 */
function normalizeCategories(string $modID, array $categories, array &$allTags) : array
{
    $keep = array();
    foreach($categories as $category)
    {
        // Prune empty categories
        if(empty($category[KEY_CAT_ITEMS])) {
            continue;
        }

        $keep[] = normalizeCategory($modID, $category, $allTags);
    }

    // Sort categories by label
    usort($keep, function(array $a, array$b) : int {
        return strnatcasecmp($a[KEY_CAT_LABEL], $b[KEY_CAT_LABEL]);
    });

    return $keep;
}

function normalizeCategory(string $modID, array $category, array &$allTags) : array
{
    // Add tags if not present
    if(!isset($category[KEY_CAT_TAGS])) {
        $category[KEY_CAT_TAGS] = array();
    }

    $category[KEY_CAT_TAGS] = normalizeTags($category[KEY_CAT_TAGS], 'mod ['.$modID.'] category ['.$category[KEY_CAT_LABEL].']');
    array_push($allTags, ...$category[KEY_CAT_TAGS]);

    foreach($category[KEY_CAT_ITEMS] as $idx => $item) {
        $normalizedItem = array(
            KEY_ITEM_NAME => $item['label'] ?? $item[KEY_ITEM_NAME] ?? '',
            KEY_ITEM_CODE => $item[KEY_ITEM_CODE] ?? ''
        );

        if(!empty($item[KEY_ITEM_TAGS])) {
            $normalizedItem[KEY_ITEM_TAGS] = normalizeTags($item[KEY_ITEM_TAGS], 'mod ['.$modID.'] category ['.$category[KEY_CAT_LABEL].'] item ['.$normalizedItem[KEY_ITEM_NAME].']');
            array_push($allTags, ...$normalizedItem[KEY_ITEM_TAGS]);
        }

        $category[KEY_CAT_ITEMS][$idx] = $normalizedItem;
    }

    // Sort items by name
    usort($category[KEY_CAT_ITEMS], function(array $a, array$b) : int {
        return strnatcasecmp($a[KEY_ITEM_NAME], $b[KEY_ITEM_NAME]);
    });

    return $category;
}

const TAG_SETS = array(
    array(
        'exclude' => array(
            'armor-pads-pack',
            'npc-accessories',
        ),
        'combinations' => array(
            array('Torso', 'Legs'),
            array('Torso', 'Feet'),
            array('Arms', 'Legs'),
            array('Arms', 'Feet'),
        ),
        'recommend' => 'Outfit',
    ),
    array(
        'exclude' => array(),
        'combinations' => array(
            array('Panties'),
            array('Bra'),
        ),
        'recommend' => 'Underwear',
    ),
    array(
        'exclude' => array(),
        'combinations' => array(
            array('Panties', 'Bra'),
        ),
        'recommend' => 'Lingerie',
    )
);

function analyzeTagRecommendations(array $tags, string $modID) : void
{
    $recommendations = array();
    foreach(TAG_SETS as $set) {
        if(in_array($set['recommend'], $tags) || in_array($modID, $set['exclude'])) {
            continue;
        }
        foreach($set['combinations'] as $combination) {
            $found = true;
            foreach($combination as $tag) {
                if(!in_array($tag, $tags)) {
                    $found = false;
                    break;
                }
            }

            if($found) {
                if(!isset($recommendations[$modID])) {
                    $recommendations[$modID] = array();
                }

                if(!in_array($set['recommend'], $recommendations[$modID])) {
                    $recommendations[$modID][] = $set['recommend'];
                }
            }
        }
    }

    foreach($recommendations as $source => $recs) {
        foreach($recs as $rec) {
            addMessage('Tag [%s] recommended for mod [%s].', $rec, $source);
        }
    }
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

function getAtelierName(string $atelierURL, string $source) : string
{
    foreach(getAteliers() as $atelier) {
        if($atelier['url'] === $atelierURL) {
            return $atelier['name'];
        }
    }

    addMessage('Unknown atelier URL [%s] in %s.', $atelierURL, $source);
    logError('Unknown atelier URL [%s].', $atelierURL);

    return '';
}

/**
 * @param string[] $tags
 * @param string $source Which part of the data file the tags are from?
 * @return string[]
 * @throws FileHelper_Exception
 */
function normalizeTags(array $tags, string $source) : array
{
    $normalized = array();
    $tagAliases = getTagAliases();

    foreach($tags as $tag)
    {
        $tag = str_replace(array('_', ' '), '-', strtolower($tag));
        if(!isset($tagAliases[$tag])) {
            addMessage('Unknown tag [%s] in %s.', $tag, $source);
            logError('Unknown tag [%s]', $tag);
            continue;
        }

        $normalized[] = $tagAliases[$tag];
    }

    sort($normalized);

    checkRequiredTags($normalized, $source);

    return $normalized;
}

/**
 * Verifies that the required tags are present for
 * each of the specified tags (as defined in the tag
 * collection in the setting {@see KEY_TAG_DEFS_REQUIRED_TAGS}).
 *
 * @param string[] $tags
 * @param string $source Which part of the data file the tags are from?
 * @return void
 * @throws FileHelper_Exception
 */
function checkRequiredTags(array $tags, string $source) : void
{
    $tagDefs = getTags();

    foreach($tags as $tag) {
        if(!isset($tagDefs[$tag])) {
            addMessage('Unknown tag [%s] in %s.', $tag, $source);
            logError('Unknown tag [%s].', $tag);
            continue;
        }

        $def = $tagDefs[$tag];

        if(empty($def[KEY_TAG_DEFS_REQUIRED_TAGS])) {
            continue;
        }

        foreach($def[KEY_TAG_DEFS_REQUIRED_TAGS] as $requiredTag) {
            if (!in_array($requiredTag, $tags)) {
                addMessage('Tag [%s] requires tag [%s] to be present too in %s.', $tag, $requiredTag, $source);
                logError('Tag [%s] requires tag [%s] to be present too.', $tag, $requiredTag);
            }
        }
    }
}

function normalizeAllMods() : void
{
    foreach(getModFiles() as $file) {
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
    if(isset($GLOBALS[GLOBAL_TAGS])) {
        return $GLOBALS[GLOBAL_TAGS];
    }

    $tags = getTagDefsFile()->getData();
    ksort($tags);

    $GLOBALS[GLOBAL_TAGS] = $tags;

    return $tags;
}

/**
 * Gets the file that stores all tag definitions.
 *
 * @return JSONFile
 * @throws FileHelper_Exception
 */
function getTagDefsFile() : JSONFile
{
    return JSONFile::factory(__DIR__.'/../../data/tags.json')
        ->setPrettyPrint(true)
        ->setEscapeSlashes(false)
        ->setTrailingNewline(true);
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

function normalizeTagDefs() : void
{
    logHeader('Normalizing tag definitions');

    $tags = getTags();

    ksort($tags);

    $result = array();
    foreach($tags as $name => $def) {
        $result[$name] = normalizeTagDef($name, $def);
    }

    getTagDefsFile()->putData($result);

    $GLOBALS[GLOBAL_TAGS] = null;

    logInfo('DONE.');
}

function normalizeTagDef(string $name, array $def) : array
{
    logInfo('- Tag [%s]...', $name);

    $result = array();

    foreach(KEYS_ORDER_TAG_DEFS as $key => $value) {
        if(isset($def[$key])) {
            $value = $def[$key];
        }

        if(empty($value)) {
            continue;
        }

        switch($key)
        {
            case KEY_TAG_DEFS_ALIASES:
            case KEY_TAG_DEFS_RELATED_TAGS:
            case KEY_TAG_DEFS_REQUIRED_TAGS:
                $value = array_unique($value);
                sort($value);
                break;

            case KEY_TAG_DEFS_LINKS:
                usort($value, static function(array $a, array $b) : int {
                    $labelA = $a[KEY_TAG_DEFS_LINKS_LABEL] ?? '';
                    $urlA = $a[KEY_TAG_DEFS_LINKS_URL] ?? '';
                    $labelB = $b[KEY_TAG_DEFS_LINKS_LABEL] ?? '';
                    $urlB = $b[KEY_TAG_DEFS_LINKS_URL] ?? '';

                    return strnatcasecmp($labelA.$urlA, $labelB.$urlB);
                });
                break;
        }

        $result[$key] = $value;
    }

    return $result;
}
