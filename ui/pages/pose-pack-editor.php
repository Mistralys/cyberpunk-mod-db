<?php

declare(strict_types=1);

namespace CPMDB\UI\Pages;

use AppUtils\ConvertHelper;
use AppUtils\Request;
use function AppLocalize\pt;
use function AppUtils\t;
use function CPDM\Assets\getImageTypes;
use function CPDM\Assets\getPosePackData;
use function CPDM\Assets\getPosePackOriginalImagesData;
use function CPDM\Assets\getPosePackScreensData;
use function CPDM\Assets\getPosePackScreensFile;
use function CPMDB\UI\getPosePackIDFromRequest;

if(!defined('CPMDB\UI\UI_ROOT')) {
    die('May not be accessed directly.');
}

const IMAGE_SIZE = 600;

$request = Request::getInstance();
$posePackID = getPosePackIDFromRequest();

if($posePackID === null) {
    die('Invalid pose pack ID.');
}

if($request->getParam('save') === 'yes')
{
    getPosePackScreensFile($posePackID)->putData(getSubmittedImages($posePackID));
    header('Location: ?page=pose-packs');
    exit;
}

/**
 * @return array<string,array{id:string,number:int,filename:string,label:string,types:string[]}>
 */
function getSubmittedImages(string $posePackID) : array
{
    $data = Request::getInstance()->getParam('images');

    if(empty($data) || !is_array($data)) {
        return array();
    }

    $existing = getPosePackScreensData($posePackID);

    $number = 0;
    foreach($data as $id => $def) {
        $existingEntry = array();
        if(isset($existing[$id])) {
            $existingEntry = $existing[$id];
        }

        $number++;
        $entry = array(
            'number' => $number,
            'label' => $def['label'],
            'filename' => sprintf('%03d', $number).'-'.ConvertHelper::transliterate($def['label']).'.png',
            'types' => $def['types']
        );

        // Preserve unknown keys
        foreach($existingEntry as $key => $value) {
            if(!isset($entry[$key])) {
                $entry[$key] = $value;
            }
        }

        $existing[$id] = $entry;
    }

    foreach($existing as $id => $def) {
        if(!isset($data[$id])) {
            unset($existing[$id]);
        }
    }

    return $existing;
}

$posePack = getPosePackData($posePackID);

?>
<link rel="stylesheet" href="css/pose-pack-editor.css"/>
<h1><?php pt('%1$s pose pack', $posePack['label']); ?></h1>
<form method="post">
    <input type="hidden" name="save" value="yes"/>
    <input type="hidden" name="page" value="pose-packs"/>
    <input type="hidden" name="pose-pack" value="<?php echo $posePackID ?>"/>
    <?php
    $imageTypes = getImageTypes();

    foreach(getPosePackOriginalImagesData($posePackID) as $image)
    {
        ?>
        <div class="image" style="width: <?php echo IMAGE_SIZE ?>px">
            <p>
                <img src="<?php echo $image['url'] ?>"/><br>
                <span class="original-filename"><?php echo $image['originalName'] ?></span>
            </p>
            <p>
                <b class="number"><?php echo sprintf('%02d', $image['number']) ?></b>
                <span class="filename"><?php echo $image['filename'] ?></span>
            </p>
            <p>
                <input
                    type="text"
                    name="images[<?php echo $image['id'] ?>][label]"
                    value="<?php echo $image['label'] ?>"
                    style="width: <?php echo IMAGE_SIZE ?>px"
                />
            </p>
            <p>
                <?php
                foreach($imageTypes as $typeID => $label) {
                    ?>
                    <label>
                        <input
                            type="checkbox"
                            name="images[<?php echo $image['id'] ?>][types][]"
                            value="<?php echo $typeID ?>"
                            <?php if(in_array($typeID, $image['types'])) { echo 'checked'; } ?>
                        />
                        <?php echo $label ?>
                    </label>
                    <br>
                    <?php
                }
                ?>
            </p>
        </div>
        <hr>
        <?php
    }
    ?>
    <p>
        <button type="submit">
            <?php echo t('Save') ?>
        </button>
        <button type="submit" name="build" value="yes">
            <?php echo t('Build') ?>
        </button>
    </p>
</form>
