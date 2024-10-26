<?php

declare(strict_types=1);

use function CPMDB\Assets\addCategory;
use function CPMDB\Assets\createNew;
use function CPMDB\Assets\generateCETCodes;
use function CPMDB\Assets\generateModsList;
use function CPMDB\Assets\getAddCategoryArg;
use function CPMDB\Assets\getCETCodesArg;
use function CPMDB\Assets\getCLICommands;
use function CPMDB\Assets\getCreateArg;
use function CPMDB\Assets\getHelpArg;
use function CPMDB\Assets\getModArg;
use function CPMDB\Assets\getModFile;
use function CPMDB\Assets\getModListArg;
use function CPMDB\Assets\getNormalizeArg;
use function CPMDB\Assets\normalizeFile;
use function CPMDB\Assets\showUsage;

require_once __DIR__ . '/assets/functions.php';
require_once __DIR__ . '/assets/prepend.php';
require_once __DIR__ . '/assets/create-new.php';
require_once __DIR__ . '/assets/add-category.php';
require_once __DIR__ . '/assets/cet-codes.php';
require_once __DIR__ . '/assets/normalize.php';
require_once __DIR__ . '/assets/generate-mods-list.php';

$commands = getCLICommands();

if(getHelpArg() !== null) {
    showUsage();
    exit;
}

$modID = getModArg();

if(!empty($modID))
{
    if(getNormalizeArg() !== null)
    {
        normalizeFile(getModFile($modID));
        exit;
    }

    $cmd = getCreateArg();
    if($cmd !== null) {
        createNew($modID, $cmd);
        exit;
    }

    $cmd = getAddCategoryArg();
    if($cmd !== null) {
        addCategory($modID, $cmd);
        exit;
    }

    if(getCETCodesArg() !== null) {
        generateCETCodes($modID);
        exit;
    }
}
else
{
    if (getModListArg() !== null) {
        generateModsList();
        exit;
    }
}

showUsage();
exit;
