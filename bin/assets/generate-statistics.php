<?php
/**
 * Script used to generate the "Statistics" section in the README
 * as well as the `statistics.json` file.
 *
 * @package CPMDB
 */

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\JSONFile;
use Exception;

function getStatisticsArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['stats'] ??
        $commands['statistics'] ??
        $commands['genstats'] ??
        $commands['generate-stats'] ??
        $commands['generate-statistics'] ??
        null;
}

function generateStatistics() : void
{
    updateReadmeStatistics();
    saveStatistics();
}

function getStatisticsFile() : JSONFile
{
    return JSONFile::factory(__DIR__.'/../../data/statistics.json')
        ->setPrettyPrint(true)
        ->setTrailingNewline(true)
        ->setEscapeSlashes(false);
}

function saveStatistics() : void
{
    getStatisticsFile()->putData(compileStatistics());
}

/**
 * @return array{mods:int, itemCategories:int, items:int, screenshots:int, ateliers:int, tags:int, authors:int}
 */
function getStatistics() : array
{
    return getStatisticsFile()->getData();
}

function updateReadmeStatistics() : void
{
    $file = FileInfo::factory(__DIR__.'/../../README.md');
    $markdown = $file->getContents();

    preg_match_all('/## Some statistics(.*)## Accessing the Database/si', $markdown, $matches);

    if(empty($matches[0])) {
        throw new Exception(
            'Could not find the "Statistics" section in the README file.'
        );
    }

    $statistics = compileStatistics();

    $text = <<<TEXT
- {mods} clothing mod details collected
- {itemCategories} item categories
- {items} individual items
- {screenshots} individually crafted screenshots
- {ateliers} known virtual atelier mods
- {tags} tags to categorize mods and items
- {authors} known mod authors
TEXT;

    $text = str_replace(
        array_map(fn($key) => '{'.$key.'}', array_keys($statistics)),
        array_values($statistics),
        $text
    );

    $markdown = str_replace($matches[1], "\n\n".$text."\n\n", $markdown);

    $file->putContents($markdown);
}

/**
 * Collects statistics on the available data in the database.
 *
 * @return array{mods:int, itemCategories:int, items:int, screenshots:int, ateliers:int, tags:int, authors:int}
 */
function compileStatistics() : array
{
    logInfo('Collecting statistics...');

    $stats = array(
        'mods' => 0,
        'itemCategories' => 0,
        'items' => 0,
        'screenshots' => 0,
        'ateliers' => count(getAteliers()),
        'tags' => count(getTags())
    );

    $authors = array();
    foreach(getModFiles() as $file)
    {
        $stats['mods']++;

        $data = $file->parse();

        if(isset($data[KEY_AUTHORS]) && is_array($data[KEY_AUTHORS])) {
            array_push($authors, ...$data[KEY_AUTHORS]);
        }

        if(isset($data[KEY_ITEM_CATEGORIES]) && is_array($data[KEY_ITEM_CATEGORIES])) {
            $cats = $data[KEY_ITEM_CATEGORIES];
            $stats['itemCategories'] += count($cats);

            foreach($cats as $cat) {
                if (isset($cat[KEY_CAT_ITEMS]) && is_array($cat[KEY_CAT_ITEMS])) {
                    $stats['items'] += count($cat[KEY_CAT_ITEMS]);
                }
            }
        }
    }

    $authors = array_map('strtolower', $authors);
    $authors = array_unique($authors);

    $stats['authors'] = count($authors);

    foreach(getScreensFolder()->getSubFiles() as $file) {
        if(in_array($file->getExtension(),array('jpg', 'png'))) {
            $stats['screenshots']++;
        }
    }

    logInfo('Done:');
    logInfo(print_r($stats, true));
    logEmptyLine();

    return $stats;
}
