<?php

require_once __DIR__.'/assets/prepend.php';

function showUsage() : void
{
    logHeader('CPMDB command line help');
    logEmptyLine();
    logInfo('# Create a new mod file skeleton');
    logInfo('> php cpmdb.php mod="mod-id" create');
    logInfo('> php cpmdb.php mod="mod-id" create="Mod name"');
    logEmptyLine();
    logInfo('# Add a new category to a mod file');
    logInfo('> php cpmdb.php mod="mod-id" add-category="Category label"');
    logEmptyLine();
    logInfo('# Display CET item codes for a mod');
    logInfo('> php cpmdb.php mod="mod-id" cet-codes');
    logEmptyLine();
    logInfo('# Normalize a mod file');
    logInfo('> php cpmdb.php mod="mod-id" normalize');
    logEmptyLine();
    logInfo('# Generate the mods list');
    logInfo('> php cpmdb.php modslist');
    logEmptyLine();
    exit;
}

$commands = getCLICommands();

if(empty($commands) || isset($commands['help']) || isset($commands['h'])) {
    showUsage();
    exit;
}

$modID = $commands['mod'] ?? $commands['m'] ?? null;

if(!empty($modID))
{
    $modID = filterModID($modID);

    if(isset($commands['normalize']))
    {
        require_once __DIR__.'/assets/normalize.php';
        normalizeFile(getModFile($modID));
        exit;
    }

    if(isset($commands['create'])) {
        require_once __DIR__ . '/assets/create-new.php';
        createNew($modID, $commands);
        exit;
    }

    if(isset($commands['add-category']) || isset($commands['addc'])) {
        require_once __DIR__ . '/assets/add-category.php';
        addCategory($modID, $commands);
        exit;
    }

    if(isset($commands['cet-codes']) || isset($commands['cet'])) {
        require_once __DIR__ . '/assets/cet-codes.php';
        \CPMDB\Assets\generateCETCodes($modID, $commands);
        exit;
    }
}
else
{
    if (isset($commands['modslist']) || isset($commands['modlist'])) {
        require_once __DIR__ . '/assets/generate-mods-list.php';
        generateModsList();
        exit;
    }
}

showUsage();
exit;
