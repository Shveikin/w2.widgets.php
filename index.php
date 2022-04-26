<?php

use Tests\component\Label;
use Tests\states\TempState;
use Widgets\state\state;
use Widgets\widget\c;
require  "./vendor/autoload.php";



echo c::app([
    new Label("Показать", 'show', 'open'),
    state::name('open')->watchin('_values', 'show', [
        new Label("молоко", 'milk'),
    ]),
]);
