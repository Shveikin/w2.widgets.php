<style>
    .tile {
        transition: all .5s ease;
        padding: 10px;
        border: 1px solid #ccc;
        margin: 5px;
        position: absolute;
        top: 0px;
    }

    .tilesBox {
        display: flex;
        position:relative;
        border: 1px solid #ccc;
        overflow: scroll;
        transition: all .5s;
        height: 70px;
        margin: 10px 0;
    } 
</style>
<?php

use Tests\app\main;
use Tests\component\iButton;
use Tests\component\Tiles;
use Widgets\state\state;
use Widgets\widget\c;

require  "./vendor/autoload.php";


echo c::app(new iButton('click', 'kl2'));