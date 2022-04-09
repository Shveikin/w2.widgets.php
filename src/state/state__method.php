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
        $state = widgetstate::name($this->stateName);
        $value = $state->get($this->props);

        return $this->render($value);
    }

    function render($value){
        switch($this->method){
            case 'watch':
                return widgetconventor::toString($value);
            break;
            default:
                return "<!-- not visual method ($this->method) -->";
            break;
        }
    }
}