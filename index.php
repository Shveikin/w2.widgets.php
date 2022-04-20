<?php

use Tests\component\iButton;
use Tests\states\TempState;
use Widgets\state\state;
use Widgets\widget\c;

require  "./vendor/autoload.php";


state::set('phrase', 'test');

echo c::app([
    state::watch('phrase'),
    iButton::c('Say hello', 'Hello world!')
]);
