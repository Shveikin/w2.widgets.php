<?php
namespace Tests\component;

use Widgets\state\state;
use Widgets\widget\widget;
use Widgets\widget\c;

class Tiles extends widget {

    function __construct(){

        $titlesState = state::name('tiles');

        $titlesState->set('_data', [
            [
                'title' => 'h1',
            ],
            [
                'title' => 'h2',
            ],
            [
                'title' => 'h3',
            ],
            [
                'title' => '43',
            ],
        ]);



        $this->child = $titlesState->map('_data', function($itm){
            return c::div($itm->title);
        });
    }
}