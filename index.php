<?php

require  "./vendor/autoload.php";
use Tests\states\TempState;
use Widgets\state\widgetstate;
use Widgets\widget\c;



echo c::app(
    ['Hello', 'message']
);