<?php

namespace Widgets\widget;

use Widgets\conventor\widgetconventor;

class c {

    private static $appcount = 0; 
    private static function getappcount(){
        return ++self::$appcount;
    }

    static function __callStatic($tag, $props){
        return new widget($tag, $props);
    }

    static function app($element){
        $widget = widgetconventor::toWidget($element);
        $html = $widget->toHTML();
        $el = $widget->toElement();

        $appcount = self::getappcount();

        $result = <<<HTML
            <div id='app____{$appcount}'>.$html.</div>
            <script>
                ${json_encode($el)}
            </script>
        HTML;


        return $result;
    }

}