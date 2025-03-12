<?php

declare(strict_types=1);

namespace CPMDB\UI;

use AppUtils\OutputBuffering;
use AppUtils\Request;
use function CPDM\Assets\posePackExists;

require_once __DIR__.'/prepend.php';

$request = Request::getInstance();

OutputBuffering::start();

switch($request->registerParam('page')->getString()) {
    case 'pose-packs':
        $id = getPosePackIDFromRequest();
        if($id !== null) {
            require_once __DIR__ . '/pages/pose-pack-editor.php';
        } else {
            require_once __DIR__ . '/pages/pose-packs-list.php';
        }
        break;

    default:
        require_once __DIR__ . '/pages/menu.php';
        break;
}

$content = OutputBuffering::get();

function getPosePackIDFromRequest() : ?string
{
    $id = Request::getInstance()->registerParam('pose-pack')->getString();

    if(!empty($id) && posePackExists($id)) {
        return $id;
    }

    return null;
}

?>
<!doctype html>
<html lang="en">
<head>
    <title>Pose Packs</title>
    <link href="css/main.css" rel="stylesheet"/>
</head>
<body style="background:#424242;color:#afafaf;">
<?php echo $content; ?>
</body>
</html>
