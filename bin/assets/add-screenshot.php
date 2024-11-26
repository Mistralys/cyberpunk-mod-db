<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper;

/**
 * @return string|null
 */
function getAddScreenshotArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['add-screenshot'] ??
        $commands['add-screenshots'] ??
        $commands['add-screen'] ??
        $commands['add-screens'] ??
        $commands['addscreen'] ??
        $commands['addscreens'] ??
        $commands['as'] ??
        $commands['adds'] ??
        null;
}

function addScreenshot(string $modID, string $screenID, ?string $label=null) : void
{
    $file = getScreenshotFile($modID);

    if($file->exists()) {
        $data = $file->getData();
    } else {
        $data = array();
    }

    $data[$screenID] = getScreenshotSkeleton($label);

    $file->putData($data);

    logHeader('Mod [%s] - Add screenshot', $modID);

    logInfo('Screenshot [%s] added successfully.', $screenID);
    logInfo('File: %s', FileHelper::relativizePath((string)$file, (string)getModRoot()));
    logEmptyLine();
}

function getScreenshotSkeleton(?string $title=null) : array
{
    return array(
        'title' => $title ?? ''
    );
}
