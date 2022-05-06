<?php

namespace Widgets\state\map;

use JsonSerializable;

class StateMapMethod implements JsonSerializable {

    private $props = [];
    private $widget;
    private $stateName;
    private $key;

    function __construct($stateName, $key, $callback){
        $imprint = new imprint();
        $this->stateName = $stateName;
        $this->key = $key;
        $this->widget = json_encode($callback($imprint));
        $this->props = $imprint->getProps();
    }

    function toElement($json = false){
        return [
            'element' => 'StateMapElement',
            'props' => [
                'stateName' => $this->stateName,
                'key' => $this->key,
                'widget' => $this->widget,
                'props' => $this->props,
            ],
            'json' => $json
        ];
    }

    function jsonSerialize(){
        return $this->toElement(true);
    }
}
