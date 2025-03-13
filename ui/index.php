<?php

declare(strict_types=1);

namespace CPMDB\UI;

use AppUtils\OutputBuffering;
use AppUtils\Request;
use function AppLocalize\pt;
use function CPMDB\UI\Assets\getPosePackIDFromRequest;
use function CPMDB\UI\Assets\resolveAction;

require_once __DIR__.'/prepend.php';
require_once __DIR__.'/assets/functions.php';

$request = Request::getInstance();

OutputBuffering::start();

switch($request->registerParam('page')->getString())
{
    case 'pose-packs':
        $id = getPosePackIDFromRequest();
        if($id !== null) {
            switch(resolveAction()) {
                case 'generate':
                    require_once __DIR__ . '/pages/pose-pack-generator.php';
                    break;
                case 'edit-images':
                default:
                    require_once __DIR__ . '/pages/pose-pack-editor.php';
                    break;
            }
        } else {
            require_once __DIR__ . '/pages/pose-packs-list.php';
        }
        break;

    default:
        require_once __DIR__ . '/pages/menu.php';
        break;
}

$content = OutputBuffering::get();

?>
<!doctype html>
<html lang="en">
    <head>
        <title><?php pt('Pose Packs'); ?></title>
        <link href="css/main.css" rel="stylesheet"/>
    </head>
    <body style="background:#424242;color:#afafaf;">
        <?php echo $content; ?>
    </body>
</html>
