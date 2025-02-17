<?php

declare(strict_types=1);

namespace CPMB\Assets;

use function CPMDB\Assets\checkScreenshots;
use function CPMDB\Assets\generateAteliersReference;
use function CPMDB\Assets\generateModsList;
use function CPMDB\Assets\generateTagsReference;
use function CPMDB\Assets\getCLICommands;
use function CPMDB\Assets\normalizeAllMods;
use function CPMDB\Assets\normalizeAteliers;
use function CPMDB\Assets\normalizeTagDefs;

function getBuildReleaseArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['build-release'] ??
        $commands['build'] ??
        $commands['buildrelease'] ??
        $commands['br'] ??
        null;
}

function buildRelease() : void
{
    normalizeAllMods();
    normalizeAteliers();
    normalizeTagDefs();

    generateModsList();
    generateTagsReference();
    generateAteliersReference();

    checkScreenshots();
}
