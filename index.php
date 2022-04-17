<?php

require  "./vendor/autoload.php";

use Tests\app\main;
use Tests\component\iButton;
use Tests\states\TempState;
use Widgets\widget\c;




// $start = microtime(true);




echo main::app([
    iButton::c('Цельсий', 'C'),
    iButton::c('Фарингейт', 'F'),
    TempState::name('Rashod')->watch('unit')
]);

