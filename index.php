<?php

use Tests\component\Label;
use Tests\states\TempState;
use Widgets\widget\c;
require  "./vendor/autoload.php";



echo c::app([
    c::h2(["Купить:", TempState::watch('_buy')]),
    new Label("молоко", 'moloko'),
    new Label("хлеб", 'hleb'),
    new Label("масло", 'maslo'),
]);
