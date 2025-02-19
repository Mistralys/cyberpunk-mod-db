<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\ConvertHelper;
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

    // Generate the list of ateliers
    foreach(getAteliers() as $atelier) {
        $lines[] = sprintf('- [%s](#%s)', $atelier[KEY_ATELIERS_NAME], ConvertHelper::transliterate($atelier[KEY_ATELIERS_NAME]));
    }

    $lines[] = '';

    // Generate the individual atelier reference
    foreach(getAteliers() as $atelier) {
        generateAtelierReference($lines, $atelier);
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

function generateAtelierReference(array &$lines, array $atelier) : void
{
    $url = $atelier[KEY_ATELIERS_URL];
    $mods = getAtelierMods($url);

    $lines[] = sprintf('### %s', $atelier[KEY_ATELIERS_NAME]);
    $lines[] = '';
    $lines[] = sprintf(
        'Mods: %s | Authors: %s | [Source](%s)',
        count($mods),
        implode(', ', $atelier[KEY_ATELIERS_AUTHORS]),
        $url
    );
    $lines[] = '';

    foreach($mods as $data) {
        if($data[KEY_ATELIER] === $url) {
            $lines[] = sprintf('- [%s](%s)', $data[KEY_MOD], $data[KEY_URL]);
        }
    }

    $lines[] = '';
}

function getAtelierMods(string $atelierURL) : array
{
    $result = array();

    foreach(getModFiles() as $file) {
        $data = $file->getData();
        if($data[KEY_ATELIER] === $atelierURL) {
            $result[] = $data;
        }
    }

    return $result;
}

