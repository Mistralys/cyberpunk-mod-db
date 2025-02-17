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
    $screenshotPath = __DIR__ . '/../../data/clothing/screens';

    foreach(getModFiles() as $file) {
        checkModScreenshots($file->getBaseName(), $screenshotPath);
    }
}

function checkModScreenshots(string $modID, string $screenshotPath) : void
{
    $path = sprintf(
        '%s/%s.jpg',
        $screenshotPath,
        $modID
    );

    if(!file_exists($path)) {
        addMessage(
            'Missing screenshot',
            'mod ['.$modID.']'
        );
        logError(
            'Missing screenshot for [%s]',
            $modID
        );
    }

    $sidecarFiles = getModScreenshotSidecarFiles($modID);
    $tags = getModTags($modID);

    // Check if mods that support MaleV have a screenshot for that version
    if(in_array('MaleV', $tags) && in_array('FemV', $tags) && !in_array($modID, IGNORE_MOD_MALE_V_SCREENSHOTS)) {
        $found = false;
        foreach($sidecarFiles as $sidecarFile) {
            $data = $sidecarFile->getData();
            if(in_array('MaleV', $data[KEY_SCREENSHOT_TAGS] ?? array())) {
                $found = true;
                break;
            }
        }

        if(!$found) {
            addMessage(
                'No screenshot detected for the MaleV version for mod [%s].',
                $modID
            );

            logInfo(
                'No screenshot detected for the MaleV version for mod [%s].',
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
