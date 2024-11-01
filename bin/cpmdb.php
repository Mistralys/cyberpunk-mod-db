<?php

declare(strict_types=1);

use function CPMB\Assets\buildRelease;
use function CPMB\Assets\getBuildReleaseArg;
use function CPMDB\Assets\addCategory;
use function CPMDB\Assets\checkScreenshots;
use function CPMDB\Assets\createNew;
use function CPMDB\Assets\generateCETCodes;
use function CPMDB\Assets\generateModsList;
use function CPMDB\Assets\generateTagsReference;
use function CPMDB\Assets\getAddCategoryArg;
use function CPMDB\Assets\getCETCodesArg;
use function CPMDB\Assets\getCheckScreenshotsArg;
use function CPMDB\Assets\getCLICommands;
use function CPMDB\Assets\getCreateArg;
use function CPMDB\Assets\getHelpArg;
use function CPMDB\Assets\getModArg;
use function CPMDB\Assets\getModFile;
use function CPMDB\Assets\getModListArg;
use function CPMDB\Assets\getNormalizeArg;
use function CPMDB\Assets\getTagsReferenceArg;
use function CPMDB\Assets\normalizeFile;
use function CPMDB\Assets\showUsage;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/assets/functions.php';
require_once __DIR__ . '/assets/prepend.php';
require_once __DIR__ . '/assets/create-new.php';
require_once __DIR__ . '/assets/add-category.php';
require_once __DIR__ . '/assets/cet-codes.php';
require_once __DIR__ . '/assets/normalize.php';
require_once __DIR__ . '/assets/check-screenshots.php';
require_once __DIR__ . '/assets/generate-mods-list.php';
require_once __DIR__ . '/assets/generate-tags-reference.php';
require_once __DIR__ . '/assets/build-release.php';

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
    if(getCheckScreenshotsArg() !== null) {
        checkScreenshots();
        exit;
    }

    if (getModListArg() !== null) {
        generateModsList();
        exit;
    }

    if(getTagsReferenceArg() !== null) {
        generateTagsReference();
        exit;
    }

    if(getBuildReleaseArg() !== null) {
        buildRelease();
        exit;
    }
}

showUsage();
exit;
