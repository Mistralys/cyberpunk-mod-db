<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper\FileInfo;

function getAteliersReferenceArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['ateliersref'] ??
        $commands['atelierref'] ??
        $commands['ateliers-ref'] ??
        $commands['atelier-ref'] ??
        $commands['ateliers-reference'] ??
        $commands['atelier-reference'] ??
        $commands['ar'] ??
        null;
}

function generateAteliersReference() : void
{
    logHeader('Generating Atelier Reference');

    $lines = array();

    foreach(getAteliers() as $atelier) {
        $lines[] = sprintf('- [%s](%s)', $atelier[KEY_ATELIERS_NAME], $atelier[KEY_ATELIERS_URL]);
    }

    $lines[] = '';

    $atelierRefFile = FileInfo::factory(__DIR__.'/../../docs/atelier-reference.md');
    $searchText = '## Available Virtual Ateliers';

    $parts = explode(
        $searchText,
        $atelierRefFile->getContents()
    );

    if(count($parts) !== 2) {
        die(sprintf('Error: File [%s] does not contain the anchor text.', $atelierRefFile->getName()));
    }

    $parts[1] = PHP_EOL.PHP_EOL.implode(PHP_EOL, $lines);

    $atelierRefFile->putContents(implode($searchText, $parts));

    logInfo('Atelier reference generated.');
}
