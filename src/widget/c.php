<?php

namespace Widgets\widget;

use Widgets\app\widgetapp;

abstract class c {

    static function __callStatic($tag, $props){
        return new widget($tag, $props);
    }

    static function app($element){
        return widgetapp::c($element);
    }

}