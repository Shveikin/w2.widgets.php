<?php

namespace Widgets\request;

use JsonSerializable;

class widgetrequest implements JsonSerializable {
    private $hash = false;
    private $then = false;

    function __construct($hash) {
        $this->hash = $hash;
    }

    function then($jsFunction){
        $this->then = $jsFunction;
        return $this;
    }

    function fetch(...$bind){
        return "requeststorage.fetch(`$this->hash`, ".json_encode($bind).")";
    }

    function bind(...$bind) {
        
        return [
            'element' => 'requeststore_element',
            'props' => [
                'hash' => $this->hash,
                'then' => $this->then,
                'bind' => $bind,
            ]
        ];
    }

    function toElement(){
        return $this->bind();
    }

    function jsonSerialize(){
        return $this->bind();
    }
}