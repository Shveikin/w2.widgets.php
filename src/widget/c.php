<?php

namespace Widgets\widget;

use Widgets\app\widgetapp;

abstract class c {

    static function __callStatic($tag, $props){
        return new widget($tag, $props);
    }

    static function app($element){
        return widgetapp::app($element);
    }

    static function js_function($body, $args = false){
        return [
            'element' => 'func', 
            'props' => [
                'body' => $body,
            ]
        ];
    }

}