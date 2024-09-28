<?php
/**
 * Script used to normalize the JSON files, by doing
 * the following:
 *
 * - Ensuring the keys are all in the same order
 * - Sorting relevant elements alphabetically
 * - Removing empty categories
 * - Adding missing tags
 *
 * The output files are stored in the subfolder `normalized`
 * to be able to review them before replacing the original files.
 *
 * @package CPMDB
 */

use AppUtils\FileHelper\JSONFile;

require_once __DIR__ . '/../vendor/autoload.php';

$files = \AppUtils\FileHelper::createFileFinder(__DIR__.'/../data/clothing')
    ->includeExtension('json')
    ->getFileInfos();

$keyOrder = array(
    'mod' => '',
    'url' => '',
    'atelier' => '',
    'authors' => array(),
    'tags' => array(),
    'itemCategories' => array()
);

foreach($files as $file) {
    if(!$file instanceof JSONFile) {
        continue;
    }

    $data = $file->parse();
    $converted = array();

    foreach($keyOrder as $key => $value) {
        $converted[$key] = $data[$key] ?? $value;
    }

    sort($converted['tags']);
    sort($converted['authors']);

    $categories = $data['itemCategories'];

    // Sort categories by label
    usort($categories, function(array $a, array$b) : int {
        return strnatcasecmp($a['label'], $b['label']);
    });

    $keep = array();
    foreach($categories as $category)
    {
        // Prune empty categories
        if(empty($category['items'])) {
            continue;
        }

        // Add tags if not present
        if(!isset($category['tags'])) {
            $category['tags'] = array();
        }

        // Sort the tags alphabetically
        sort($category['tags']);

        // Sort items by name
        usort($category['items'], function(array $a, array$b) : int {
            return strnatcasecmp($a['name'], $b['name']);
        });

        $keep[] = $category;
    }

    $converted['itemCategories'] = $keep;

    $newFile = JSONFile::factory($file->getFolderPath().'/normalized/'. $file->getName())->putData($converted, true);

    $json = file_get_contents((string)$newFile);
    $json = str_replace('\/', '/', $json);
    $json .= PHP_EOL;

    file_put_contents((string)$newFile, $json);
}
