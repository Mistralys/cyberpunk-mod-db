<?php

require_once __DIR__.'/assets/prepend.php';

function showUsage() : void
{
    logInfo('Usage:');
    logInfo('');
    logInfo('# Create a new mod file skeleton');
    logInfo('php cpmdb.php create=mod_id name="Mod name"');
    logInfo('');
    logInfo('# Add a new category to a mod file');
    logInfo('php cpmdb.php add-category=mod_id label="Category label"');
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

if(isset($commands['normalize']))
{
    require_once __DIR__.'/assets/normalize.php';

    if(!empty($commands['normalize'])) {
        $modID = $commands['normalize'];
        normalizeFile(getModFile($modID));
        exit;
    }

    normalizeAll();
    exit;
}

if(isset($commands['modslist']) || isset($commands['modlist'])) {
    require_once __DIR__.'/assets/generate-mods-list.php';
    generateModsList();
    exit;
}

$createID = $commands['create'] ?? null;
if(!empty($createID)) {
    require_once __DIR__ . '/assets/create-new.php';
    createNew($createID, $commands);
    exit;
}

$addCategoryID = $commands['add-category'] ?? $commands['addc'] ?? null;
if(!empty($addCategoryID)) {
    require_once __DIR__ . '/assets/add-category.php';
    addCategory($addCategoryID, $commands);
    exit;
}

showUsage();
exit;
