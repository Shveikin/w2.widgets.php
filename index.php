<?php

use Tests\app\main;
use Tests\component\Label;
use Tests\states\TempState;
use Widgets\state\state;
use Widgets\widget\c;
require  "./vendor/autoload.php";


echo main::app([
    TempState::watch('_buy'),
    TempState::watch('update'),
    c::button('click', onclick: TempState::_set('update', '***'))
]);
