<?php

use Tests\component\Label;
use Tests\states\TempState;
use Widgets\widget\c;
require  "./vendor/autoload.php";



echo c::app([
    c::h2(["Купить:", TempState::watch('_buy')]),
    new Label("молоко", 52),
    new Label("хлеб", 53),
    new Label("масло", 54),
]);
