<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper\JSONFile;
use AppUtils\FileHelper_Exception;

function showUsage() : void
{
    logHeader('CPMDB command line help');
    logEmptyLine();
    logInfo('# Create a new mod file skeleton');
    logInfo('mod="mod-id" create');
    logInfo('mod="mod-id" create="Mod name"');
    logEmptyLine();
    logInfo('# Add a new category to a mod file');
    logInfo('mod="mod-id" add-category="Category label"');
    logEmptyLine();
    logInfo('# Display CET item codes for a mod');
    logInfo('mod="mod-id" cet-codes');
    logEmptyLine();
    logInfo('# Normalize a mod file');
    logInfo('mod="mod-id" normalize');
    logEmptyLine();
    logInfo('# Generate the mods list');
    logInfo('modslist');
    logEmptyLine();
    logInfo('# Generate the tags reference');
    logInfo('tagsref');
    logEmptyLine();
}

function getHelpArg() : ?string
{
    $commands = getCLICommands();

    if(empty($commands)) {
        return '';
    }

    return
        $commands['help'] ??
        $commands['h'] ??
        $commands['?'] ??
        null;
}

function getModArg() : ?string
{
    $commands = getCLICommands();

    $id =
        $commands['mod'] ??
        $commands['m'] ??
        $commands['modid'] ??
        $commands['mod-id'] ??
        null;

    if(!empty($id)) {
        return filterModID($id);
    }

    return null;
}

/**
 * @return JSONFile[]
 * @throws FileHelper_Exception
 */
function getFiles() : array
{
    return FileHelper::createFileFinder(__DIR__.'/../../data/clothing')
        ->includeExtension('json')
        ->getFiles()
        ->typeJSON();
}

/**
 * Ensures that the mod ID is in a valid format, without
 * spaces or special characters.
 *
 * @param string $modID
 * @return string
 */
function filterModID(string $modID) : string
{
    return ConvertHelper::transliterate($modID);
}

function logHeader(string $header, ...$args) : void
{
    logEmptyLine();
    logSeparator();
    logInfo($header, ...$args);
    logSeparator();
    logEmptyLine();
}

function logSeparator() : void
{
    echo str_repeat('-', 70).PHP_EOL;
}

function logEmptyLine() : void
{
    echo PHP_EOL;
}

function logInfo(string $message, ...$args) : void
{
    echo sprintf($message, ...$args).PHP_EOL;
}

function logError(string $message, ...$args) : void
{
    echo '...! '.sprintf($message, ...$args).PHP_EOL;
}

/**
 * @return array<string,string>
 */
function getCLICommands() : array
{
    global $argv;
    static $commands = null;

    if(isset($commands)) {
        return $commands;
    }

    $commands = array();
    $first = true;
    foreach($argv as $arg)
    {
        if($first === true) {
            $first = false;
            continue;
        }

        $arg = trim($arg, '-/');
        $parts = ConvertHelper::explodeTrim('=', $arg);
        $value = '';
        $name = strtolower($parts[0]);

        if(count($parts) == 2) {
            $value = $parts[1];
        }

        $commands[$name] = $value;
    }

    return $commands;
}

function getModFile(string $id) : JSONFile
{
    return JSONFile::factory(__DIR__.'/../../data/clothing/'.$id.'.json')
        ->setEscapeSlashes(false)
        ->setTrailingNewline(true);
}

function getCategorySkeleton(?string $label=null) : array
{
    return array(
        'label' => $label ?? '',
        'tags' => array(),
        'items' => array(
            array(
                'name' => 'ItemName',
                'code' => 'item_code',
                'tags' => array()
            )
        )
    );
}

function getAteliersFile() : JSONFile
{
    return JSONFile::factory(__DIR__.'/../../data/ateliers.json')
        ->setPrettyPrint(true)
        ->setTrailingNewline(true)
        ->setEscapeSlashes(false);
}

/**
 * Gets all atelier information, sorted by name.
 * @return array<string,array{url:string,name:string,authors:string[]}>
 * @throws FileHelper_Exception
 */
function getAteliers() : array
{
    if(isset($GLOBALS['__ateliers'])) {
        return $GLOBALS['__ateliers'];
    }

    $ateliers = getAteliersFile()->getData();

    uasort($ateliers, function($a, $b) {
        return strcasecmp($a[KEY_ATELIERS_NAME], $b[KEY_ATELIERS_NAME]);
    });

    $GLOBALS['__ateliers'] = $ateliers;

    return $ateliers;
}

/**
 * Gets the path to the folder containing all screenshots.
 * @return FolderInfo
 * @throws FileHelper_Exception
 */
function getScreensFolder() : FolderInfo
{
    return FolderInfo::factory(__DIR__.'/../../data/clothing/screens');
}

/**
 * Gets the path to the screenshot file for the specified mod.
 *
 * NOTE: Use {@see JSONFile::exists()} to check if the file exists.
 *
 * @param string $modID
 * @return JSONFile
 * @throws FileHelper_Exception
 */
function getScreenshotFile(string $modID) : JSONFile
{
    return JSONFile::factory(getScreensFolder().'/'.$modID.'.json')
        ->setPrettyPrint(true)
        ->setTrailingNewline(true)
        ->setEscapeSlashes(false);
}

function getModRoot() : FolderInfo
{
    return FolderInfo::factory(__DIR__.'/../../');
}
