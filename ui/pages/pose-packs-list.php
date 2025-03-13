<?php

declare(strict_types=1);

namespace CPMDB\UI\Pages;

use function AppLocalize\pt;
use function CPDM\Assets\getPosePacksData;

if(!defined('CPMDB\UI\UI_ROOT')) {
    die('May not be accessed directly.');
}

?>
<link rel="stylesheet" href="css/pose-pack-editor.css"/>
<h1><?php pt('Available pose packs') ?></h1>
<ul>
<?php

foreach(getPosePacksData() as $pack) {
    ?>
    <li class="pose-pack-entry">
        <a href="?page=pose-packs&action=edit-images&pose-pack=<?php echo $pack['id']; ?>"><?php echo $pack['label']; ?></a>
    </li>
    <?php
}

?>
</ul>
