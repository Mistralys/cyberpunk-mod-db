<?php
/**
 * Script used to generate the `modslist.md` file from the JSON files.
 *
 * @package CPMDB
 */

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper\FileInfo;

function getModListArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['modlist'] ??
        $commands['ml'] ??
        $commands['modslist'] ??
        $commands['mods-list'] ??
        $commands['mod-list'] ??
        null;
}

function generateModsList() : void
{
    $list = array();
    foreach (getModFiles() as $file) {
        $data = $file->parse();
        $list[] = array(
            'label' => $data['mod'],
            'url' => $data['url'],
            'authors' => $data['authors']
        );
    }

    usort($list, function (array $a, array $b): int {
        return strnatcasecmp($a['label'], $b['label']);
    });

    $lines = array();

    foreach ($list as $entry) {
        $lines[] = sprintf(
            '* [%s](%s) by %s',
            $entry['label'],
            $entry['url'],
            ConvertHelper::implodeWithAnd($entry['authors'], ', ', ' and ')
        );
    }

    file_put_contents(
        __DIR__ . '/../../mods-list.md',
        '## Mods List' . PHP_EOL . PHP_EOL .
        'Total available mods: '. count($list) . PHP_EOL . PHP_EOL .
        implode(PHP_EOL, $lines) . PHP_EOL
    );

    logInfo('Mods list generated successfully.');
    logInfo('There are %s total mods.', count($list));

    updateReadmeModCount(count($list));
}

function updateReadmeModCount(int $count) : void
{
    $readmeFile = FileInfo::factory(__DIR__ . '/../../README.md');

    $readmeFile->putContents(preg_replace(
        '/Total available mods: \d+/',
        'Total available mods: ' . $count,
        $readmeFile->getContents()
    ));

    logInfo('Updated the mod count in the readme.');
}
