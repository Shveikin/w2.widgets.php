<?php

require  "./vendor/autoload.php";

use Tests\component\iButton;
use Tests\states\TempState;
use Widgets\state\widgetstate;


$tmp = TempState::get('unit');

// echo iButton::c('Hello');