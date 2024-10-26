<?php
/**
 * Script used to generate the `modslist.md` file from the JSON files.
 *
 * @package CPMDB
 */

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\ConvertHelper;

require_once __DIR__ . '/prepend.php';

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
    foreach (getFiles() as $file) {
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
}
