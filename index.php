<?php

require  "./vendor/autoload.php";

use Tests\app\main;
use Tests\component\iButton;
use Tests\states\TempState;
use Widgets\app\widgetapp;
use Widgets\widget\c;

// $tmp1 = TempState::get('unit');
// $tmp2 = TempState::name('Rashod')->get('unit');
// $tmp3 = TempState::name('Pressure')->get('unit');



echo main::app([
    TempState::name('Rashod')->watch('unit'),
    iButton::c('Цельсий', 'C'),
    iButton::c('Фарингейт', 'F'),
]);




// echo "$tmp1 - $tmp2 - $tmp3";
// echo iButton::c('Hello');