<?php

namespace Widgets\state;

use Widgets\conventor\widgetconventor;

class state__method {

    function __construct($stateName, $method, $props){
        $this->stateName = $stateName;
        $this->method = $method;
        $this->props = $props;
    }

    function __toString(){
        return $this->toHTML();
    }

    function toHTML(){
        $state = widgetstate::name($this->stateName);
        $values = '';
        foreach($this->props as $key){
            $values = $state->get($key);
        }

        return $this->render($values);
    }



    function render($value){
        switch($this->method){
            case 'watch':
                return $value;
            break;
            case 'watchdefault':
                return $value;
            break;
            default:
                return "<!-- not visual method ($this->method) -->";
            break;
        }
    }


    function toElement(){
        return [
            'element' => 'StateMethod',
            'props' => [
                'method' => $this->method,
                'args'  => $this->props,
                'stateName' => $this->stateName,
            ]
        ];
    }
}