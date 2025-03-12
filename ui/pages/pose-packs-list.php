<?php

declare(strict_types=1);

namespace CPMDB\UI\Pages;

use function AppLocalize\pt;
use function CPDM\Assets\getPosePacksData;

if(!defined('CPMDB\UI\UI_ROOT')) {
    die('May not be accessed directly.');
}

?>
<p>
    <?php pt('Please choose a pose pack to manage.'); ?>
</p>
<ul>
<?php

foreach(getPosePacksData() as $pack) {
    ?>
    <li>
        <a href="?page=pose-packs&pose-pack=<?php echo $pack['id']; ?>">
            <?php echo $pack['label']; ?>
        </a>
    </li>
    <?php
}

?>
</ul>
