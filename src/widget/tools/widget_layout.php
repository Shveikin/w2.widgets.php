<?php

namespace Widgets\widget\tools;

use DI2\DI2;
use Widgets\widget\widget;

class widget_layout {
    
    private widget $widget;

    function __construct($widget){
        $this->widget = $widget;
    }

    function __set($prop, $value){
        if ($prop=='child'){
            array_push($this->widget->child, $value);
        } else {
            $this->widget->props[$prop] = $value;
        }
    }

    function __get($prop){
        if ($prop=='child')
            return $this->widget->child;
        else
            return $this->widget->props[$prop];
    }



    function set(...$props){
        foreach ($props as $key => $value) {
            $this->{$key} = $value;
        }
    }

    function childs(...$childs){
        foreach ($childs as $child) {
            $this->child = $child;
        }
    }

}