<?php

require  "./vendor/autoload.php";

use Tests\component\iButton;
use Tests\states\TempState;
use Widgets\state\widgetstate;

$tmp1 = TempState::get('unit');

$tmp2 = TempState::name('Rashod')->get('unit');
$tmp3 = TempState::name('Pressure')->get('unit');
echo "$tmp1 - $tmp2 - $tmp3";
// echo iButton::c('Hello');