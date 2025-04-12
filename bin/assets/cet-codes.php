<?php

declare(strict_types=1);

namespace CPMDB\Assets;

/**
 * @return string|null
 */
function getCETCodesArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['cet-codes'] ??
        $commands['cet'] ??
        null;
}

function getCategoryArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['category'] ??
        $commands['cat'] ??
        $commands['c'] ??
        null;
}

/**
 * @param string $modID
 * @param string|null $limitCategory Optional category to limit the output to.
 * @return void
 */
function generateCETCodes(string $modID, ?string $limitCategory=null) : void
{
    $data = getModFile($modID)->getData();
    $result = array();

    $categories = $data['itemCategories'] ?? array();

    foreach($categories as $category)
    {
        if($limitCategory !== null && ($category[KEY_CAT_ID] !== $limitCategory && $category[KEY_CAT_LABEL] !== $limitCategory)) {
            continue;
        }

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
