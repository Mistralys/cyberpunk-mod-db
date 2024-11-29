<?php

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper\FileInfo;

function getTagsReferenceArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['tagsref'] ??
        $commands['tagref'] ??
        $commands['tags-ref'] ??
        $commands['tags-reference'] ??
        $commands['tr'] ??
        null;
}

function generateTagsReference() : void
{
    $lines = array();

    foreach(getTagsCategorized() as $category => $tags) {
        $lines[] = sprintf('### %s', $category);
        $lines[] = '';
        foreach($tags as $tagName => $tagDef) {
            generateModLines($lines, $tagName, $tagDef);
        }
        $lines[] = '';
    }

    $tagRefFile = FileInfo::factory(__DIR__.'/../../docs/tagging-reference.md');
    $searchText = '## Available Tags reference';

    $parts = explode(
        $searchText,
        $tagRefFile->getContents()
    );

    if(count($parts) !== 2) {
        die(sprintf('Error: File [%s] does not contain the anchor text.', $tagRefFile->getName()));
    }

    $parts[1] = PHP_EOL.PHP_EOL.implode(PHP_EOL, $lines);

    $tagRefFile->putContents(implode($searchText, $parts));

    logInfo('Tags reference generated.');
}

function generateModLines(array &$lines, string $tagName, array $tagDef) : void
{
    $links = $tagDef[KEY_TAG_DEFS_LINKS] ?? array();
    $description = $tagDef[KEY_TAG_DEFS_DESCRIPTION];

    if(isset($tagDef[KEY_TAG_DEFS_FULL_NAME])) {
        $description = '"'.$tagDef[KEY_TAG_DEFS_FULL_NAME].'" - '.$description;
    }

    if($tagName !== $description) {
        $line = sprintf(
            '- `%s` - %s',
            $tagName,
            $description
        );
    } else {
        $line = sprintf(
            '- `%s`',
            $tagName
        );
    }

    if(!empty($links)) {
        foreach($links as $link) {
            $line .= sprintf(
                ' [%s](%s)',
                $link[KEY_TAG_DEFS_LINKS_LABEL],
                $link[KEY_TAG_DEFS_LINKS_URL]
            );
        }
    }

    $lines[] = $line;
}
