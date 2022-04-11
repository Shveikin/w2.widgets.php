<?php

namespace Widgets\widget;

use Widgets\conventor\widgetconventor;

class c {

    static function __callStatic($tag, $props){
        return new widget($tag, $props);
    }

    static function app($element){
        $widget = widgetconventor::toWidget($element);
        $html = $widget->toHTML();

        return $html;
    }

}