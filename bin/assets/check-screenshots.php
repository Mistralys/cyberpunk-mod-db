<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper;
use AppUtils\FileHelper\JSONFile;

const GLOBAL_SCREEN_SIDECAR_FILES = '__cpmdb_screen_sidecar_files';

/**
 * Mods where not having a screenshot for the
 * MaleV version can be ignored.
 */
const IGNORE_MOD_MALE_V_SCREENSHOTS = array(
    'xtx-sneakers',
    'biker-boots',
    'leather-boots',
    'military-boots'
);

function getCheckScreenshotsArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['check-screenshots'] ??
        $commands['check-screens'] ??
        $commands['checkscreens'] ??
        $commands['checkshots'] ??
        $commands['cs'] ??
        null;
}

function checkScreenshots() : void
{
    logHeader('Checking screenshots');

    $screenshotPath = __DIR__ . '/../../data/clothing/screens';

    foreach(getModFiles() as $file) {
        $modID = $file->getBaseName();
        checkModScreenshots($modID, $screenshotPath);
        checkModItemScreenshots($modID, $screenshotPath);
    }

    logInfo('DONE.');
    logEmptyLine();
}

function checkModItemScreenshots(string $modID, string $screenshotPath) : void
{
    $data = getModFile($modID)->getData();

    foreach($data[KEY_ITEM_CATEGORIES] as $category) {
        if(!isset($category[KEY_CAT_ID])) {
            addMessage('ERROR | Mod [%s] | Missing category ID.', $modID);
            logError('  ...Missing category ID.', $modID);
            continue;
        }

        $categoryID = $category[KEY_CAT_ID];

        $icon = $category[KEY_CAT_ICON] ?? '';
        if(empty($icon)) {
            addMessage('ItemIcons | Mod [%s] | Category [%s] No icon specified.', $modID, $categoryID);
            logInfo('  ...Missing icon for [%s].', $categoryID);
            continue;
        }

        checkItemIcon($modID, $categoryID, $icon, $screenshotPath);
    }
}

function checkItemIcon(string $modID, string $categoryID, string $iconName, $screenshotPath) : void
{
    $path = sprintf(
        '%s/%s-item-%s',
        $screenshotPath,
        $modID,
        $iconName
    );

    foreach(array('jpg', 'png') as $ext) {
        if(file_exists($path.'.'.$ext)) {
            return;
        }
    }

    addMessage(
        'MissingImages | Mod [%s] | Category [%s] | Icon image [%s] is missing.',
        $modID,
        $categoryID,
        basename($path)
    );

    logInfo('  ...Missing icon image [%s].', $iconName);
}

function checkModScreenshots(string $modID, string $screenshotPath) : void
{
    logInfo('- Mod [%s]...', $modID);

    $path = sprintf(
        '%s/%s.jpg',
        $screenshotPath,
        $modID
    );

    if(!file_exists($path)) {
        addMessage('ModScreenshots | Mod [%s] | Screenshot image is missing.', $modID);
        logInfo('  ...the screenshot is missing.');
        return;
    }

    $sidecarFiles = getModScreenshotSidecarFiles($modID);
    $tags = getModTags($modID);

    // Check if mods that support MaleV have a screenshot for that version
    if(in_array('MaleV', $tags) && in_array('FemV', $tags) && !in_array($modID, IGNORE_MOD_MALE_V_SCREENSHOTS)) {
        $found = false;
        foreach($sidecarFiles as $sidecarFile) {
            $data = $sidecarFile->getData();
            foreach($data as $value) {
                if(!empty($value[KEY_SCREENSHOT_TAGS]) && in_array('MaleV', $value[KEY_SCREENSHOT_TAGS])) {
                    $found = true;
                    break;
                }
            }
        }

        if(!$found) {
            addMessage(
                'ModScreenshots | Mod [%s] | No screenshot detected for the MaleV version.',
                $modID
            );

            logInfo(
                '  ...No screenshot detected for the MaleV version.',
                $modID
            );
        }
    }
}

/**
 * @param string $modID
 * @return JSONFile[]
 */
function getModScreenshotSidecarFiles(string $modID) : array
{
    $files = getScreenshotSidecarFiles();
    $result = array();

    foreach($files as $baseName => $file) {
        if(str_starts_with($baseName, $modID)) {
            $result[] = $file;
        }
    }

    return $result;
}

/**
 * @return array<string, JSONFile>
 */
function getScreenshotSidecarFiles() : array
{
    if(isset($GLOBALS[GLOBAL_SCREEN_SIDECAR_FILES])) {
        return $GLOBALS[GLOBAL_SCREEN_SIDECAR_FILES];
    }

    $files = FileHelper::createFileFinder(__DIR__.'/../../data/clothing/screens')
        ->includeExtension('json')
        ->getFiles()
        ->typeJSON();

    $result = array();
    foreach($files as $file) {
        $result[$file->getBaseName()] = $file;
    }

    $GLOBALS[GLOBAL_SCREEN_SIDECAR_FILES] = $result;

    return $result;
}
