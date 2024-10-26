<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper_Exception;

require_once __DIR__.'/prepend.php';

/**
 * @return string|null
 */
function getAddCategoryArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['add-category'] ??
        $commands['addc'] ??
        $commands['addcat'] ??
        $commands['ac'] ??
        $commands['addcategory'] ??
        null;
}

/**
 * @param string $modID
 * @return void
 * @throws FileHelper_Exception
 */
function addCategory(string $modID, string $label) : void
{
    $file = getModFile($modID);
    $data = $file->parse();

    $data['itemCategories'][] = getCategorySkeleton($label);

    $file->putData($data, true);

    logHeader('Mod [%s] - Add category', $modID);

    logInfo('Category [%s] added successfully.', $label);
    logEmptyLine();
}
