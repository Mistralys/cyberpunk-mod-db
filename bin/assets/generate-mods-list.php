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
            'file' => $file->getName(),
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
            '* [%s](%s) by %s ([source](%s))',
            $entry['label'],
            'data/clothing/'.$entry['file'],
            ConvertHelper::implodeWithAnd($entry['authors'], ', ', ' and '),
            $entry['url']
        );
    }

    file_put_contents(
        __DIR__ . '/../../mods-list.md',
        '# Mod JSON files' . PHP_EOL . PHP_EOL .
        'Total available files: '. count($list) . PHP_EOL . PHP_EOL .
        implode(PHP_EOL, $lines) . PHP_EOL
    );

    logInfo('Mods list generated successfully.');
    logInfo('There are %s total mods.', count($list));
}
