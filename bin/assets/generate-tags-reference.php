<?php

declare(strict_types=1);

namespace CPMDB\Assets;

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

    file_put_contents(__DIR__.'/../../build-tags-reference.md', implode("\n", $lines));

    logInfo('Tags reference generated.');
}

function generateModLines(array &$lines, string $tagName, array $tagDef) : void
{
    $links = $tagDef['links'] ?? array();
    $description = $tagDef['description'];

    if(isset($tagDef['fullName'])) {
        $description = '"'.$tagDef['fullName'].'" - '.$description;
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
            $line .= sprintf(' [%s](%s)', $link['label'], $link['url']);
        }
    }

    $lines[] = $line;
}
