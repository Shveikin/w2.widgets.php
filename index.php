<?php

require  "./vendor/autoload.php";

use Tests\app\main;


echo main::app([
    ['Hello', 'world']
]);

