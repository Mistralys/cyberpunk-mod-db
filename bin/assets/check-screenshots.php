<?php

declare(strict_types=1);

namespace CPMDB\Assets;

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

    foreach(getFiles() as $file) {
        $path = sprintf(
            '%s/%s.jpg',
            $screenshotPath,
            $file->getBaseName()
        );

        if(!file_exists($path)) {
            addMessage(
                'Missing screenshot',
                'mod ['.$file->getBaseName().']'
            );
            logError(
                'Missing screenshot for [%s]',
                $file->getBaseName()
            );
        }
    }
}
