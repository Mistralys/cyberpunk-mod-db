<?php

declare(strict_types=1);

use AppUtils\FileHelper_Exception;

require_once __DIR__.'/prepend.php';

/**
 * @param string $modID
 * @param array<string,string> $commands
 * @return void
 * @throws FileHelper_Exception
 */
function addCategory(string $modID, array $commands) : void
{
    $label = $commands['add-category'] ?? $commands['label'] ?? null;

    $file = getModFile($modID);
    $data = $file->parse();

    $data['itemCategories'][] = getCategorySkeleton($label);

    $file->putData($data, true);

    logHeader('Mod [%s] - Add category', $modID);

    logInfo('Category [%s] added successfully.', $label);
    logEmptyLine();
}
