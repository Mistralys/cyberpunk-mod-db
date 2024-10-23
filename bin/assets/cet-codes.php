<?php

declare(strict_types=1);

namespace CPMDB\Assets;

/**
 * @param string $modID
 * @param array<string,string> $commands
 * @return void
 */
function generateCETCodes(string $modID, array $commands) : void
{
    $data = getModFile($modID)->getData();
    $result = array();

    $categories = $data['itemCategories'] ?? array();

    foreach($categories as $category) {
        $items = $category['items'] ?? array();

        foreach($items as $item) {
            $result[] = sprintf(
                'Game.AddToInventory("Items.%s", 1)',
                $item['code']
            );
        }
    }

    usort($result, 'strnatcasecmp');

    logHeader('Mod [%s] - CET Item Codes', $modID);

    echo implode(PHP_EOL, $result).PHP_EOL;
}
