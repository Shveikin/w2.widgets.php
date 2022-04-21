<?php

use Tests\component\iButton;
use Widgets\state\state;
use Widgets\widget\c;
require  "./vendor/autoload.php";



echo c::app([
    state::watch('title'),
    new iButton('test', '__test'), iButton::c('app', '__app'), iButton::c('ccc', '__ccc')
]);
