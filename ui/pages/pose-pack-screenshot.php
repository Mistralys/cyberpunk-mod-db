<?php

declare(strict_types=1);

namespace CPMDB\UI\Pages;

use AppUtils\FileHelper\FileInfo;
use AppUtils\OutputBuffering;
use AppUtils\Request;
use function CPDM\Assets\getPosePackOriginalImagesFolder;
use function CPMDB\UI\Assets\requirePosePackFromRequest;

if(!defined('CPMDB\UI\UI_ROOT')) {
    die('May not be accessed directly.');
}

$posePackID = requirePosePackFromRequest();
$screenName = htmlentities(Request::getInstance()->registerParam('screenshot-name')->getString());
$file = FileInfo::factory(getPosePackOriginalImagesFolder($posePackID).'/'.$screenName);

OutputBuffering::stop();

if($file->exists()) {
    $file->send($file->getName());
    exit;
}

http_response_code(404);
exit;


