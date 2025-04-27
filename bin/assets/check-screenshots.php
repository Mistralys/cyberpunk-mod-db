<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper;
use AppUtils\FileHelper\JSONFile;

const GLOBAL_SCREEN_SIDECAR_FILES = '__cpmdb_screen_sidecar_files';

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

    foreach(getModFiles() as $file) {
        $modID = $file->getBaseName();
        checkModScreenshots($modID);
        checkModItemScreenshots($modID);
    }

    logInfo('DONE.');
    logEmptyLine();
}

function getScreenshotsPath() : string
{
    return __DIR__ . '/../../data/clothing/screens';
}

function checkModItemScreenshots(string $modID) : void
{
    logInfo('- Mod [%s] | Checking item screenshots...', $modID);

    $data = getModFile($modID)->getData();
    $messages = false;

    foreach($data[KEY_ITEM_CATEGORIES] as $category) {
        if(!isset($category[KEY_CAT_ID])) {
            $messages = true;
            addMessage('ERROR | Mod [%s] | Missing category ID.', $modID);
            logError('  ...Missing category ID.', $modID);
            continue;
        }

        $categoryID = $category[KEY_CAT_ID];

        if(!isset($category[KEY_CAT_ICON])) {
            $messages = true;
            addMessage('ItemIcons | Mod [%s] | Category [%s] | The icon key is missing.', $modID, $categoryID);
            logInfo('  ...The `icon` key is missing for [%s].', $categoryID);
            continue;
        }

        $icon = $category[KEY_CAT_ICON];
        if(empty($icon)) {
            $messages = true;
            addMessage('ItemIcons | Mod [%s] | Category [%s] No icon specified.', $modID, $categoryID);
            logInfo('  ...Missing icon for [%s].', $categoryID);
            continue;
        }

        checkItemIcon($modID, $categoryID, $icon);
    }

    if(!$messages) {
        logInfo('  ...All OK.');
    }
}

function checkItemIcon(string $modID, string $categoryID, string $iconName) : void
{
    $path = sprintf(
        '%s/%s-item-%s',
        getScreenshotsPath(),
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

function checkModScreenshots(string $modID) : void
{
    logInfo('- Mod [%s] | Checking screenshots...', $modID);

    $path = sprintf(
        '%s/%s.jpg',
        getScreenshotsPath(),
        $modID
    );

    if(!file_exists($path)) {
        addMessage('ModScreenshots | Mod [%s] | Screenshot image is missing.', $modID);
        logInfo('  ...the screenshot is missing.');
        return;
    }

    $sidecarFiles = getModScreenshotSidecarFiles($modID);
    $tags = getModTags($modID);
    $messages = false;

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
            $messages = true;
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

    if(!$messages) {
        logInfo('  ...All OK.');
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
