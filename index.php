<?php

use Tests\app\main;
use Tests\component\Tiles;

require  "./vendor/autoload.php";

echo main::app(new Tiles());
