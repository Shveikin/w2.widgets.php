<?php

namespace Widgets\state;

use Widgets\tools\Container;



abstract class state {
    use Container;

    private $data = false;
    private $default = [];
    private $alias = false;

    private $name = false;

    function __parent(){
        widgetstate::reg($this);
    }

    function setName($name){
        if ($this->name==false)
            $this->name = $name;
    }

    protected function set($key, $value){
        $this->data[$key] = $value;
    }

    protected function get($key){
        return $this->data[$key];
    }

    protected function default(...$data){
        $this->default = $data;
        if ($this->data == false){
            $this->data = $data;
        }
    }

    protected function getdefault($key){
        return $this->default[$key];
    }

    protected function alias(...$url_key){
        $this->alias = $url_key;
    }

    function __call($method, $props){
        return new state__method($this->name, $method, $props);
    }
}