<?php

use Tests\component\Label;
use Tests\states\TempState;
use Widgets\state\state;
use Widgets\widget\c;
require  "./vendor/autoload.php";

state::set('__element', new Label('Hello', 'dd'));

echo c::app([
    state::watch('__element')
]);
