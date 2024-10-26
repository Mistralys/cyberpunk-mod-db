<?php
/**
 * Utility script to create a new mod file skeleton.
 *
 * @package CPMDB
 */

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper_Exception;

require_once __DIR__.'/prepend.php';

/**
 * @return string|null
 */
function getCreateArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['create'] ??
        $commands['c'] ??
        null;
}

/**
 * @param string $modID
 * @return void
 * @throws FileHelper_Exception
 */
function createNew(string $modID, string $name) : void
{
    if (empty($modID)) {
        logError('Mod ID not specified.');
        showUsage();
        exit;
    }

    $data = array(
        'mod' => $name,
        'url' => '',
        'atelier' => '',
        'authors' => array('AuthorName'),
        'tags' => array(
            'Clothing',
            'Body-Vanilla',
            'FemV'
        ),
        'comments' => 'OptionalComments',
        'linkedMods' => array(
            'mod-id'
        ),
        'seeAlso' => array(
            array(
                'url' => 'https://example.com',
                'label' => 'Link label'
            )
        ),
        'itemCategories' => array(
            getCategorySkeleton()
        )
    );

    getModFile($modID)->putData($data, true);

    logInfo('Mod [%s] created successfully.' . PHP_EOL, $modID);
}
