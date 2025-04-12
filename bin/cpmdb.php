<?php

declare(strict_types=1);

namespace CPMDB\Bin;

use function CPMB\Assets\buildRelease;
use function CPMB\Assets\getBuildReleaseArg;
use function CPMDB\Assets\addCategory;
use function CPMDB\Assets\addScreenshot;
use function CPMDB\Assets\buildPoses;
use function CPMDB\Assets\checkMod;
use function CPMDB\Assets\checkScreenshots;
use function CPMDB\Assets\createNew;
use function CPMDB\Assets\displayMessages;
use function CPMDB\Assets\generateAteliersReference;
use function CPMDB\Assets\generateCETCodes;
use function CPMDB\Assets\generateModsList;
use function CPMDB\Assets\generatePoseReferenceDoc;
use function CPMDB\Assets\generatePosePosters;
use function CPMDB\Assets\cropPosePackImages;
use function CPMDB\Assets\generateStatistics;
use function CPMDB\Assets\generateTagsReference;
use function CPMDB\Assets\getAddCategoryArg;
use function CPMDB\Assets\getAddScreenshotArg;
use function CPMDB\Assets\getAteliersReferenceArg;
use function CPMDB\Assets\getBuildPosesArg;
use function CPMDB\Assets\getCategoryArg;
use function CPMDB\Assets\getCETCodesArg;
use function CPMDB\Assets\getCheckModArg;
use function CPMDB\Assets\getCheckScreenshotsArg;
use function CPMDB\Assets\getCLICommands;
use function CPMDB\Assets\getCreateArg;
use function CPMDB\Assets\getGeneratePosterRowsArg;
use function CPMDB\Assets\getCropPoseImagesArg;
use function CPMDB\Assets\getHelpArg;
use function CPMDB\Assets\getModArg;
use function CPMDB\Assets\getModFile;
use function CPMDB\Assets\getModListArg;
use function CPMDB\Assets\getNormalizeAllArg;
use function CPMDB\Assets\getNormalizeArg;
use function CPMDB\Assets\getNormalizeAteliersArg;
use function CPMDB\Assets\getNormalizeTagsArg;
use function CPMDB\Assets\getPosePackArg;
use function CPMDB\Assets\getPoseReferenceDocArg;
use function CPMDB\Assets\getStatisticsArg;
use function CPMDB\Assets\getTagsReferenceArg;
use function CPMDB\Assets\normalizeAllMods;
use function CPMDB\Assets\normalizeAteliers;
use function CPMDB\Assets\normalizeFile;
use function CPMDB\Assets\normalizeTagDefs;
use function CPMDB\Assets\showUsage;

require_once __DIR__ . '/../boostrap.php';

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
        done();
    }

    $cmd = getCreateArg();
    if($cmd !== null) {
        createNew($modID, $cmd);
        done();
    }

    $cmd = getAddCategoryArg();
    if($cmd !== null) {
        addCategory($modID, $cmd);
        done();
    }

    $cmd = getAddScreenshotArg();
    if($cmd !== null) {
        addScreenshot($modID, $cmd);
        done();
    }

    if(getCETCodesArg() !== null) {
        generateCETCodes($modID, getCategoryArg());
        done();
    }

    if(getCheckModArg() !== null) {
        checkMod($modID);
        done(true);
    }
}

$posePackID = getPosePackArg();

if(!empty($posePackID))
{
    if(getCropPoseImagesArg() !== null) {
        cropPosePackImages($posePackID);
        done();
    }

    if(getGeneratePosterRowsArg() !== null) {
        generatePosePosters($posePackID);
        done();
    }
}

if(getCheckScreenshotsArg() !== null) {
    checkScreenshots();
    done();
}

if (getModListArg() !== null) {
    generateModsList();
    done();
}

if(getTagsReferenceArg() !== null) {
    generateTagsReference();
    done();
}

if(getAteliersReferenceArg() !== null) {
    generateAteliersReference();
    done();
}

if(getBuildReleaseArg() !== null) {
    buildRelease();
    done();
}

if(getNormalizeAllArg() !== null) {
    normalizeAllMods();
    done();
}

// Normalize the atelier definitions file
if(getNormalizeAteliersArg() !== null) {
    normalizeAteliers();
    done();
}

// Normalize the tag definitions file
if (getNormalizeTagsArg() !== null) {
    normalizeTagDefs();
    done();
}

if(getPoseReferenceDocArg() !== null) {
    generatePoseReferenceDoc();
    done();
}

if(getBuildPosesArg() !== null) {
    buildPoses();
    done();
}

if(getStatisticsArg() !== null) {
    generateStatistics();
    done();
}

showUsage();
exit;

function done(bool $forceMessages=false) : never
{
    $commands = getCLICommands();

    $show =
        $commands['show-messages'] ??
        $commands['showmessages'] ??
        $commands['showmsg'] ??
        $commands['messages'] ??
        $commands['msg'] ??
        null;

    if($forceMessages || $show !== null) {
        displayMessages();
    }

    exit;
}
