<?php
/**
 * Functions for accessing pose pack data.
 *
 * @package CPDM
 */

declare(strict_types=1);

namespace CPDM\Assets;

use AppUtils\BaseException;
use AppUtils\ConvertHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper\JSONFile;
use function AppUtils\t;

function getImageTypes() : array
{
    return array(
        'narrow' => t('Width:') . ' ' . t('Narrow'),
        'medium' => t('Width:') . ' ' . t('Medium'),
        'wide' => t('Width:') . ' ' . t('Wide'),
        'sitting' => t('Sitting'),
        'lying-down' => t('Lying down'),
        'action' => t('Action')
    );
}

function getPosePackScreensFile(string $posePackID) : JSONFile
{
    return JSONFile::factory(getPosePackFolder($posePackID).'/screenshots.json')
        ->setTrailingNewline(true)
        ->setPrettyPrint(true)
        ->setEscapeSlashes(false);
}

/**
 * @param string $posePackID
 * @return string[]
 */
function getPosePackScreenIDs(string $posePackID) : array
{
    return array_keys(getPosePackScreensData($posePackID));
}

/**
 * @param string $posePackID
 * @param string $screenID
 * @return array{number:int,outputName:string,originalName:string,label:string,types:string[]}
 */
function getPosePackScreenData(string $posePackID, string $screenID) : array
{
    return getPosePackScreensData($posePackID)[$screenID] ?? array();
}

/**
 * @param string $posePackID
 * @return array<string,array{number:int,outputName:string,originalName:string,label:string,types:string[]}>
 */
function getPosePackScreensData(string $posePackID) : array
{
    $file = getPosePackScreensFile($posePackID);

    if($file->exists()) {
        return $file->getData();
    }

    return array();
}

/**
 * Gets all source images available for a pose
 * pack, from its `Screens` folder.
 *
 * @param string $posePackID
 * @return FileInfo[]
 */
function getPosePackOriginalImages(string $posePackID) : array
{
    $images = getPosePackOriginalImagesFolder($posePackID)
        ->createFileFinder()
        ->includeExtension('png')
        ->getFiles()
        ->typeANY();

    usort($images, function ($a, $b) : int {
        return strnatcasecmp($a->getPath(), $b->getPath());
    });

    return $images;
}

const ORIGINAL_SCREENS_FOLDER = 'Screens';
const BUILD_SCREENS_FOLDER = 'Build';

/**
 * @param string $posePackID
 * @return array<string,array{id:string,url:string,label:string,types:string[],filename:string,sourceFileName:string}>
 */
function getPosePackOriginalImagesData(string $posePackID) : array
{
    $existing = getPosePackScreensData($posePackID);

    $result = array();

    $number = 0;
    foreach (getPosePackOriginalImages($posePackID) as $image) {
        $number++;
        $id = ConvertHelper::transliterate($image->getName());

        $entry = array(
            'id' => $id,
            'number' => $number,
            'url' => '../docs/Poses/'.$posePackID.'/'.ORIGINAL_SCREENS_FOLDER.'/' . $image->getName(),
            'label' => '',
            'types' => array(''),
            'outputName' => '(' . t('save to generate file name') . ')',
            'originalName' => $image->getName()
        );

        if (isset($existing[$id])) {
            $entry['number'] = $existing[$id]['number'] ?? $number;
            $entry['label'] = $existing[$id]['label'] ?? '';
            $entry['types'] = $existing[$id]['types'] ?? array();
            $entry['outputName'] = $existing[$id]['outputName'] ?? '';
        }

        $result[$id] = $entry;
    }

    return $result;
}

/**
 * Gets all image files from the Pose Pack's output folder.
 *
 * @param string $posePackID
 * @return FileInfo[]
 */
function getPosePackOutputImages(string $posePackID) : array
{
    $folder = getPosePackImagesOutputFolder($posePackID);
    if(!$folder->exists()) {
        return array();
    }

    return $folder
        ->createFileFinder()
        ->includeExtension('png')
        ->getFiles()
        ->typeANY();
}

function getPosePackImagesOutputFolder(string $posePackID) : FolderInfo
{
    return FolderInfo::factory(getPosePackFolder($posePackID).'/'.BUILD_SCREENS_FOLDER);
}

function getPosePackOriginalImagesFolder(string $posePackID) : FolderInfo
{
    return FolderInfo::factory(getPosePackFolder($posePackID).'/'.ORIGINAL_SCREENS_FOLDER);
}

function getPosePackFolder(string $posePackID) : FolderInfo
{
    return FolderInfo::factory(getPosesFolder().'/'.$posePackID);
}

function getPosesFolder() : FolderInfo
{
    return FolderInfo::factory(__DIR__.'/../../docs/Poses');
}

function getPosePacksDataFile() : JSONFile
{
    return JSONFile::factory(getPosesFolder().'/pose-packs.json')
        ->setTrailingNewline(true)
        ->setPrettyPrint(true)
        ->setEscapeSlashes(false);
}

function posePackExists(string $posePackID) : bool
{
    return in_array($posePackID, getPosePackIDs());
}

function getPosePackIDs() : array
{
    $result = array();

    foreach(getPosePacksData() as $pack) {
        $result[] = $pack['id'];
    }

    return $result;
}

/**
 * @param string $posePackID
 * @return array{id:string,label:string,cutX:int,cutY:int,cutHeight:int}
 * @throws BaseException {@see ERROR_POSE_PACK_NOT_FOUND}
 */
function getPosePackData(string $posePackID) : array
{
    $packs = getPosePacksData();

    foreach($packs as $pack) {
        if($pack['id'] === $posePackID) {
            return $pack;
        }
    }

    throw new BaseException(
        'Pose pack not found.',
        sprintf('Pose pack with ID "%s" not found.', $posePackID),
        ERROR_POSE_PACK_NOT_FOUND
    );
}

const ERROR_POSE_PACK_NOT_FOUND = 175401;

/**
 * Gets all pose packs data.
 * @return array<int,array{id:string,label:string,cutX:int,cutY:int,cutHeight:int}>
 */
function getPosePacksData() : array
{
    $result = array();

    foreach(getPosePacksDataFile()->getData() as $id => $entry) {
        if(!is_array($entry)) {
            continue;
        }

        $result[] = array(
            'id' => $id,
            'label' => $entry['label'] ?? '',
            'cutX' => $entry['cutX'] ?? 0,
            'cutY' => $entry['cutY'] ?? 0,
            'cutHeight' => $entry['cutHeight'] ?? 0
        );
    }

    usort($result, function (array $a, array $b) : int {
        return strnatcasecmp($a['label'], $b['label']);
    });

    return $result;
}

/**
 * @return array<int,array{label:string,images:FileInfo[]}>
 */
function getPoseReferencePosterImages() : array
{
    $result = array();
    foreach(getPosePacksData() as $posePack) {
        $folder = getPosePackFolder($posePack['id']);
        $result[] = array(
            'posePackID' => $posePack['id'],
            'label' => $posePack['label'],
            'images' => $folder
                ->createFileFinder()
                ->includeExtension('jpg')
                ->getFiles()
                ->typeANY()
        );
    }

    return $result;
}
