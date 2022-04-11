<?php

namespace Widgets\state;

use Widgets\conventor\widgetconventor;

class state__method {
    function __construct($stateName, $method, $props){
        $this->stateName = $stateName;
        $this->method = $method;
        $this->props = $props;
    }

    function toHTML(){
        $state = widgetstate::name($this->stateName);
        $values = '';
        foreach($this->props as $key){
            $values = $state->get($key);
        }

        return $this->render($values);
    }

    function toElement(){
        return [
            'element' => $this->method,
            'props' => $this->props,
            'state' => $this->stateName,
        ];
    }

    function render($value){
        switch($this->method){
            case 'watch':
                return $value;
            break;
            default:
                return "<!-- not visual method ($this->method) -->";
            break;
        }
    }
}