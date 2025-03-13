<?php

declare(strict_types=1);

namespace CPMDB\UI\Assets;

use AppUtils\Request;
use function CPDM\Assets\posePackExists;

function getPosePackIDFromRequest() : ?string
{
    $id = Request::getInstance()->registerParam('pose-pack')->getString();

    if(!empty($id) && posePackExists($id)) {
        return $id;
    }

    return null;
}

function requirePosePackFromRequest() : string
{
    $id = getPosePackIDFromRequest();
    if($id !== null) {
        return $id;
    }

    header('Location: ?page=pose-packs');
    exit;
}

function resolveAction() : string
{
    return Request::getInstance()->registerParam('action')->getString();
}
