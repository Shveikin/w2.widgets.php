<?php
namespace Tests\component;

use Widgets\state\state;
use Widgets\widget\widget;
use Widgets\widget\c;

class Tiles extends widget {

    function __construct(){

        $titlesState = state::name('tiles');

        $titlesState->set('_data', [
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],            
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],            
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],            
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
            ['title' => 'h1',],
            ['title' => 'h2',],
            ['title' => 'h3',],
            ['title' => '43',],
        ]);


        $size = 62;

        $map = $titlesState->map('_data', function($itm) use($titlesState, $size) {
            return c::div(
                $itm->title,
                style: $titlesState->watchif('open', true, 
                    '
                        left: ' . $itm->calc("@key % Math.floor(window.document.body.clientWidth / $size) * 60") .'px;
                        top: '. $itm->calc("Math.floor(@key / Math.floor(window.document.body.clientWidth / $size)) * 60")  .'px;
                    ', 
                    '
                        left: ' . $itm->calc('@key * 45') .'px;
                    '
                ),
                className: 'tile',
            );
        });

        $this->child = c::div(
            $map,
            style: $titlesState->watchif('open', true, 'height: 500px;'),
            className: 'tilesBox'
        );
    }
}