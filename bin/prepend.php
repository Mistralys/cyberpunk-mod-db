<?php

use AppUtils\ConvertHelper;
use AppUtils\FileHelper;
use AppUtils\FileHelper\JSONFile;
use AppUtils\FileHelper_Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @return JSONFile[]
 * @throws FileHelper_Exception
 */
function getFiles() : array
{
    $files = FileHelper::createFileFinder(__DIR__.'/../data/clothing')
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

function getCLICommands() : array
{
    global $argv;

    $commands = array();
    foreach($argv as $arg)
    {
        if(strpos($arg, __FILE__)) {
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
