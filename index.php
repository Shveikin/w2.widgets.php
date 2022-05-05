<?php

use Tests\app\main;
use Tests\component\HideBox;

require  "./vendor/autoload.php";

echo main::app([
    HideBox::c('hl world'),
    HideBox::c('otherText'),
    HideBox::c('hello'),
]);
