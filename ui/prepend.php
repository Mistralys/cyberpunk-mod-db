<?php

declare(strict_types=1);

namespace CPMDB\UI;

use const CPMDB\Config\CPMDB_SOURCES_FOLDER;

const UI_ROOT = __DIR__;

require_once __DIR__ . '/../boostrap.php';

if(CPMDB_SOURCES_FOLDER === '') {
    die(sprintf('To use the pose pack UI, the %s constant must be defined in the config.php file.', 'CPMDB\Config\CPMDB_SOURCES_FOLDER'));
}
