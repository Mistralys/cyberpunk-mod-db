<?php

declare(strict_types=1);

namespace CPMB\Assets;

use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\JSONFile;
use Mistralys\ChangelogParser\ChangelogParser;
use Mistralys\VersionParser\VersionParser;
use function CPMDB\Assets\checkScreenshots;
use function CPMDB\Assets\generateAteliersReference;
use function CPMDB\Assets\generateModsList;
use function CPMDB\Assets\generateStatistics;
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
    generateStatistics();
    updateVersion();

    checkScreenshots();
}

/**
 * Detects the current database version number by
 * parsing the changelog file and using the latest
 * entry.
 *
 * @return VersionParser
 */
function detectVersion() : VersionParser
{
    return ChangelogParser::parseMarkdownFile(FileInfo::factory(__DIR__.'/../../changelog.md'))
        ->requireLatestVersion()
        ->getVersionInfo();
}

/**
 * Updates the version number in the package.json file
 * and the version.txt file.
 */
function updateVersion() : void
{
    $version = detectVersion();

    $packageFile = JSONFile::factory(__DIR__.'/../../package.json')
        ->setPrettyPrint(true)
        ->setTrailingNewline(true)
        ->setEscapeSlashes(false);

    $data = $packageFile->getData();

    $data["version"] = $version->getTagVersion();

    $packageFile->putData($data);

    getVersionFile()->putContents($version->getTagVersion());
}

/**
 * Gets the database version.
 * @return string Version string, e.g. `4.1.2`.
 */
function getVersion() : string
{
    return getVersionFile()->getContents();
}

/**
 * Gets the path to the `version.txt` file that contains
 * the current database version.
 *
 * @return FileInfo
 */
function getVersionFile() : FileInfo
{
    return FileInfo::factory(__DIR__.'/../../version.txt');
}

