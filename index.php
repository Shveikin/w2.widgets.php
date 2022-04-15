<?php

require  "./vendor/autoload.php";

use Tests\app\main;
use Tests\component\iButton;
use Tests\states\TempState;
use Widgets\widget\c;

// $tmp1 = TempState::get('unit');
// $tmp2 = TempState::name('Rashod')->get('unit');
// $tmp3 = TempState::name('Pressure')->get('unit');

// $start = microtime(true);

$content = c::div();

for ($i=0; $i < 1; $i++) { 
    $content->child = TempState::name('Rashod')->watchdefault('unit', '1', '0');
}

$content->style = 'display: flex;';

echo main::app([
    iButton::c('Цельсий', 'C'),
    iButton::c('Фарингейт', 'F'),
    $content
]);



// echo '['. (microtime(true) - $start) . ']';

// echo "$tmp1 - $tmp2 - $tmp3";
// echo iButton::c('Hello');