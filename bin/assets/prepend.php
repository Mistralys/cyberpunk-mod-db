<?php

use AppUtils\ConvertHelper;
use AppUtils\FileHelper;
use AppUtils\FileHelper\JSONFile;
use AppUtils\FileHelper_Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * @return JSONFile[]
 * @throws FileHelper_Exception
 */
function getFiles() : array
{
    $files = FileHelper::createFileFinder(__DIR__.'/../../data/clothing')
        ->includeExtension('json')
        ->getFileInfos();

    $result =array();

    foreach($files as $file) {
        if ($file instanceof JSONFile) {
            $result[] = $file;
        }
    }

    return $result;
}

function logInfo(string $message, ...$args) : void
{
    echo sprintf($message, ...$args).PHP_EOL;
}

function logError(string $message, ...$args) : void
{
    echo '...! '.sprintf($message, ...$args).PHP_EOL;
}

/**
 * @return array<string,string>
 */
function getCLICommands() : array
{
    global $argv;

    $commands = array();
    $first = true;
    foreach($argv as $arg)
    {
        if($first === true) {
            $first = false;
            continue;
        }

        $arg = trim($arg, '-/');
        $parts = ConvertHelper::explodeTrim('=', $arg);
        $value = '';
        $name = $parts[0];

        if(count($parts) == 2) {
            $value = $parts[1];
        }

        $commands[$name] = $value;
    }

    return $commands;
}

function getModFile(string $id) : JSONFile
{
    return JSONFile::factory(__DIR__.'/../../data/clothing/'.$id.'.json')
        ->setEscapeSlashes(false)
        ->setTrailingNewline(true);
}

function getCategorySkeleton(?string $label=null) : array
{
    return array(
        'label' => $label ?? '',
        'tags' => array(),
        'items' => array(
            array(
                'name' => 'ItemName',
                'code' => 'item_code',
                'tags' => array()
            )
        )
    );
}
