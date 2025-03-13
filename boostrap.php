<?php

require_once __DIR__ . '/vendor/autoload.php';

if(file_exists(__DIR__.'/config.php')) {
    require_once __DIR__.'/config.php';
}

if(!defined('CPMDB\Config\CPMDB_SOURCES_FOLDER')) {
    define("CPMDB\Config\CPMDB_SOURCES_FOLDER", '');
}
