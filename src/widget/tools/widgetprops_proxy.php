<?php

namespace Widgets\widget\tools;

use DI2\DI2;
use Widgets\widget\widget;

class widgetprops_proxy {
    
    private widget $widget;

    function __construct($widget){
        $this->widget = $widget;
    }

    function __set($prop, $value){
        $er = explode('#', new \ErrorException('test', 0, 56, __FILE__, __LINE__))[1];

        if ($prop=='child'){
            if (!isset($this->widget->props['child']))
                $this->widget->props['child'] = [];

            array_push($this->widget->props['child'], $value);
        } else {
            $this->widget->props[$prop] = $value;
        }
    }

    function __get($prop){
        return $this->widget->props[$prop];
    }

}