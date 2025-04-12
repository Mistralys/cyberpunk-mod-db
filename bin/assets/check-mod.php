<?php

declare(strict_types=1);

namespace CPMDB\Assets;

/**
 * @return string|null
 */
function getCheckModArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['check'] ??
        $commands['check-mod'] ??
        $commands['checkmod'] ??
        $commands['cm'] ??
        null;
}

function checkMod(string $modID) : void
{
    checkModScreenshots($modID);
    checkModItemScreenshots($modID);
}
