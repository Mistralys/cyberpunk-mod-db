<?php
/**
 * Functions for generating pose pack reference
 * images and docs from the existing data.
 *
 * @package CPDM
 */

declare(strict_types=1);

namespace CPMDB\Assets;

use AppUtils\FileHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\ImageHelper;
use AppUtils\ImageHelper_Size;
use function CPDM\Assets\getPosePackData;
use function CPDM\Assets\getPosePackFolder;
use function CPDM\Assets\getPosePackIDs;
use function CPDM\Assets\getPosePackImagesOutputFolder;
use function CPDM\Assets\getPosePackOriginalImagesFolder;
use function CPDM\Assets\getPosePackOutputImages;
use function CPDM\Assets\getPosePackScreenData;
use function CPDM\Assets\getPosePackScreenIDs;
use function CPDM\Assets\getPoseReferencePosterImages;
use function CPDM\Assets\getPosesFolder;
use function CPDM\Assets\posePackExists;

function getPosePackArg() : ?string
{
    $commands = getCLICommands();

    $id =
        $commands['pose-pack'] ??
        $commands['ppid'] ??
        null;

    if(!empty($id) && posePackExists($id)) {
        return $id;
    }

    return null;
}

function getGeneratePosterRowsArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['generate-posters'] ??
        $commands['genposters'] ??
        $commands['posters'] ??
        null;
}

function getPoseReferenceDocArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['refdoc'] ??
        $commands['ref-doc'] ??
        $commands['reference-doc'] ??
        $commands['ref-document'] ??
        $commands['reference-document'] ??
        null;
}

function getBuildPosesArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['buildposes'] ??
        $commands['build-poses'] ??
        $commands['bp'] ??
        null;
}

function getCropPoseImagesArg() : ?string
{
    $commands = getCLICommands();

    return
        $commands['crop'] ??
        $commands['crop-images'] ??
        $commands['cropimg'] ??
        null;
}

function cropPosePackImages(string $posePackID) : void
{
    $packData = getPosePackData($posePackID);

    logHeader('Generating Pose Pack "'.$packData['label'].'"');

    // Clean the output folder and ensure that it exists
    $outputFolder = getPosePackImagesOutputFolder($posePackID);
    FileHelper::deleteTree(FolderInfo::factory($outputFolder));
    $outputFolder->create();

    foreach(getPosePackScreenIDs($posePackID) as $screenID) {
        generatePoseImage($posePackID, $screenID);
    }
}

/**
 * @param string $posePackID
 * @param string $screenID
 * @return void
 */
function generatePoseImage(string $posePackID, string $screenID) : void
{
    $screen = getPosePackScreenData($posePackID, $screenID);
    $pack = getPosePackData($posePackID);

    logInfo('- Screen #%s %s', $screen['number'], $screen['label']);

    $sourceFile = FileInfo::factory(getPosePackOriginalImagesFolder($posePackID).'/'.$screen['originalName']);
    $targetFile = FileInfo::factory(getPosePackImagesOutputFolder($posePackID).'/'.$screen['outputName']);

    $image = ImageHelper::createFromFile($sourceFile);
    $image->addRGBColor('black', 0, 0, 0);
    $image->addRGBColor('white', 255, 255, 255);

    $ratio = array(950, 1440);

    $posX = $pack['cutX'];
    $posY = $pack['cutY'];
    $height = $pack['cutHeight'];
    $width = (int)floor(($height * $ratio[0]) / $ratio[1]);

    $image->crop($width, $height, $posX, $posY);

    $barHeight = 80;
    $fontSize = 32;
    $textPaddingLeft = 20;
    $textTopOffset = 56;
    $labelOffset = 70;

    $barTop = $image->getHeight();
    $barBottom = $barTop + $barHeight;
    $image->crop($image->getWidth(), $barBottom);


    $image->fill('black', 0, $barTop);

    $image->setFontTTF(__DIR__.'/fonts/Nunito/black.ttf');

    $image->textTTF(
        sprintf('%02d', $screen['number']),
        $fontSize + 2,
        'white',
        $textPaddingLeft,
        $barTop + $textTopOffset
    );

    $image->setFontTTF(__DIR__.'/fonts/Nunito/bold.ttf');

    $image->textTTF(
        $screen['label'],
        $fontSize,
        'white',
        $textPaddingLeft+ $labelOffset,
        $barTop + $textTopOffset
    );

    $image->save((string)$targetFile);

    //echo $targetFile.PHP_EOL;
    //exit;
}

function generatePosePosters(string $posePackID) : void
{
    logHeader('Generate poster images for %s', $posePackID);

    $images = getPosePackOutputImages($posePackID);
    if(empty($images)) {
        logInfo('No images found.', $posePackID);
        return;
    }

    logInfo('Found [%s] images.', count($images));

    $total = count($images);
    $posesPerRow = 5;
    $rows = (int)ceil($total / $posesPerRow);
    $referenceSize = ImageHelper::getImageSize((string)$images[0]);
    $borderSize = 2;
    $posterWidth = ($referenceSize->getWidth() * $posesPerRow) + $borderSize * ($posesPerRow + 1);
    $posterHeight = $referenceSize->getHeight() + ($borderSize * 2);

    for($row=0; $row < $rows; $row++)
    {
        $start = $row * $posesPerRow;
        $end = $start + $posesPerRow;

        if($start === 0) {
            $start = 1;
        }

        logInfo('- Poses %02d to %02d', $start, $end);

        $posterImages = array_slice($images, $row * $posesPerRow, $posesPerRow);

        $posterImage = ImageHelper::createNew($posterWidth, $posterHeight, 'jpeg');
        $posterImage->setQuality(80);
        $posterImage->addRGBColor('black', 0, 0, 0);
        $posterImage->fill('black', 0, $posterHeight);

        $xpos = $borderSize;
        $ypos = $borderSize;
        foreach($posterImages as $image)
        {
            $poseImage = ImageHelper::createFromFile($image);

            $posterImage->paste($poseImage, $xpos, $ypos);
            $xpos += $poseImage->getWidth() + $borderSize;

            $poseImage->dispose();
        }

        $targetFile = getPosePackFolder($posePackID).'/'.sprintf('%02d-%02d', $start, $end).'.jpg';

        $posterImage->save($targetFile);
    }

    logInfo('DONE.');
    logEmptyLine();
}

function generatePoseReferenceDoc() : void
{
    logHeader('Generate Pose Reference Document');

    $posePacks = getPoseReferencePosterImages();

    $markdown = "# Pose Reference\n\n";

    foreach($posePacks as $pack)
    {
        logInfo('- Pose pack %s', $pack['label']);

        $markdown .= "## {$pack['label']}\n\n";
        $markdown .= "[Pose pack mod homepage]({$pack['url']})\n\n";
        foreach($pack['images'] as $image) {
            $markdown .= "![{$image->getBaseName()}]({$pack['posePackID']}/{$image->getName()})\n";
        }

        $markdown .= "\n";
    }

    FileInfo::factory(getPosesFolder().'/pose-reference.md')
        ->putContents($markdown);

    logInfo('DONE.');
}

function buildPoses() : void
{
    foreach(getPosePackIDs() as $posePackID) {
        cropPosePackImages($posePackID);
        generatePosePosters($posePackID);
    }

    generatePoseReferenceDoc();
}
