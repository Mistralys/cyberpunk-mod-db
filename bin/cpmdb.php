<?php

declare(strict_types=1);

use function CPMB\Assets\buildRelease;
use function CPMB\Assets\getBuildReleaseArg;
use function CPMDB\Assets\addCategory;
use function CPMDB\Assets\addScreenshot;
use function CPMDB\Assets\buildPoses;
use function CPMDB\Assets\checkScreenshots;
use function CPMDB\Assets\createNew;
use function CPMDB\Assets\displayMessages;
use function CPMDB\Assets\generateAteliersReference;
use function CPMDB\Assets\generateCETCodes;
use function CPMDB\Assets\generateModsList;
use function CPMDB\Assets\generatePoseReferenceDoc;
use function CPMDB\Assets\generatePosePosters;
use function CPMDB\Assets\cropPosePackImages;
use function CPMDB\Assets\generateTagsReference;
use function CPMDB\Assets\getAddCategoryArg;
use function CPMDB\Assets\getAddScreenshotArg;
use function CPMDB\Assets\getAteliersReferenceArg;
use function CPMDB\Assets\getBuildPosesArg;
use function CPMDB\Assets\getCETCodesArg;
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
use function CPMDB\Assets\getTagsReferenceArg;
use function CPMDB\Assets\normalizeAllMods;
use function CPMDB\Assets\normalizeAteliers;
use function CPMDB\Assets\normalizeFile;
use function CPMDB\Assets\normalizeTagDefs;
use function CPMDB\Assets\showUsage;

require_once __DIR__ . '/../vendor/autoload.php';

$commands = getCLICommands();

if(getHelpArg() !== null) {
    showUsage();
    exit;
}

$modID = getModArg();
$posePackID = getPosePackArg();

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

    $cmd = getAddScreenshotArg();
    if($cmd !== null) {
        addScreenshot($modID, $cmd);
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

    if(getAteliersReferenceArg() !== null) {
        generateAteliersReference();
        exit;
    }

    if(getBuildReleaseArg() !== null) {
        buildRelease();
        displayMessages();
        exit;
    }

    if(getNormalizeAllArg() !== null) {
        normalizeAllMods();
        displayMessages();
        exit;
    }

    // Normalize the atelier definitions file
    if(getNormalizeAteliersArg() !== null) {
        normalizeAteliers();
        displayMessages();
        exit;
    }

    // Normalize the tag definitions file
    if (getNormalizeTagsArg() !== null) {
        normalizeTagDefs();
        displayMessages();
        exit;
    }
}

if(!empty($posePackID))
{
    if(getCropPoseImagesArg() !== null) {
        cropPosePackImages($posePackID);
        exit;
    }

    if(getGeneratePosterRowsArg() !== null) {
        generatePosePosters($posePackID);
        exit;
    }
}
else
{
    if(getPoseReferenceDocArg() !== null) {
        generatePoseReferenceDoc();
        exit;
    }

    if(getBuildPosesArg() !== null) {
        buildPoses();
        exit;
    }
}

showUsage();
exit;
