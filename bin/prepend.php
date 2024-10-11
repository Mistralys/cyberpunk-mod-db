<?php

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