<?php

namespace Widgets\request;


class widgetrequest {
    private $hash = false;
    private $then = false;

    function __construct($hash) {
        $this->hash = $hash;
    }

    function then($jsFunction){
        $this->then = $jsFunction;
        return $this;
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
}