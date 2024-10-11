<?php
/**
 * Utility script to create a new mod file skeleton.
 *
 * @package CPMDB
 */

declare(strict_types=1);

use AppUtils\FileHelper\JSONFile;

require_once __DIR__.'/prepend.php';

$commands = getCLICommands();

if(isset($commands['help']) || isset($commands['h'])) {
    showUsage();
}

$id = $commands['id'] ?? '';
$name = $commands['name'] ?? '';

if(empty($id)) {
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
    'itemCategories' => array(
        array(
            'label' => '',
            'tags' => array(),
            'items' => array(
                array(
                    'label' => 'ItemName',
                    'code' => 'item_code',
                    'tags' => array()
                )
            )
        )
    )
);

JSONFile::factory(__DIR__.'/../data/clothing/'.$id.'.json')
    ->putData($data, true);

logInfo('Mod [%s] created successfully.'.PHP_EOL, $id);

function showUsage() : void
{
    logInfo('Usage: php create-new.php id=mod_id name="Mod name"');
    exit;
}
