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


$btn = c::button('hello', style: 'color: #f00');
echo main::c($btn);



// echo "$tmp1 - $tmp2 - $tmp3";
// echo iButton::c('Hello');