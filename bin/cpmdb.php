<?php

require_once __DIR__.'/assets/prepend.php';

function showUsage() : void
{
    logInfo('Usage:');
    logInfo('');
    logInfo('# Create a new mod file skeleton');
    logInfo('php cpmdb.php create=mod_id name="Mod name"');
    logInfo('');
    logInfo('# Normalize all mod files');
    logInfo('php cpmdb.php normalize');
    logInfo('');
    logInfo('# Generate the mods list');
    logInfo('php cpmdb.php modslist');
    logInfo('');
    exit;
}

$commands = getCLICommands();

if(empty($commands) || isset($commands['help']) || isset($commands['h'])) {
    showUsage();
    exit;
}

if(isset($commands['normalize'])) {
    require_once __DIR__.'/assets/normalize.php';
    normalizeAll();
    exit;
}

if(isset($commands['modslist'])) {
    require_once __DIR__.'/assets/generate-mods-list.php';
    generateModsList();
    exit;
}

if(isset($commands['create'])) {
    require_once __DIR__ . '/assets/create-new.php';
    createNew($commands);
    exit;
}
