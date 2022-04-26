<?php

namespace Widgets\state;

use JsonSerializable;
use Widgets\conventor\widgetconventor;

/** 
 * Обертка для передачи данных на фронтенд
*/
class state__method implements JsonSerializable {

    function __construct($stateName, $method, $props){
        $this->stateName = $stateName;
        $this->method = $method;

        $this->props = array_map(
            function($itm){
                return widgetconventor::toElement($itm);
            }, 
            (array)$props
        );
    }

    function __toString(){
        return $this->toHTML();
    }

    function toHTML(){
        $state = widgetstate::name($this->stateName);
        $values = '';

        $isWidget = false;
        foreach($this->props as $key){
            if (str_starts_with($key, '__'))
                $isWidget  = true;

            $values = $state->get($key);
        }

        return $this->render($values, $isWidget);
    }



    function render($value, $isWidget){
        switch($this->method){
            case 'watch':
            case 'watchdefault':
                if ($isWidget){
                    return widgetconventor::toHTML($value);
                } else {
                    return $value;
                }
            break;
            default:
                return "<!-- not visual method ($this->method) -->";
            break;
        }
    }


    function toElement($json = false){
        $result = [
            'element' => 'StateMethod',
            'props' => [
                'method' => $this->method,
                'args'  => $this->props,
                'stateName' => $this->stateName,
                'json' => $json,
            ]
        ];
        return $result;
    }

    function jsonSerialize(){
        return $this->toElement(true);
    }

}