<?php

require  "./vendor/autoload.php";

use Tests\app\main;
use Tests\component\iButton;
use Tests\states\TempState;
use Widgets\widget\c;




// $start = microtime(true);

TempState::name('Rashod')->set('unit', '*');


echo main::app([
    iButton::c('Цельсий', 'C'),
    TempState::name('Rashod')->watch('unit')
]);

