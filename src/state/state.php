<?php

namespace Widgets\state;

use DI2\Container;



class state {
    use Container;

    private $data = false;
    private $default = [];
    private $alias = false;

    private $name = false;


    function __construct(
        $super = false, 
        $default = false
    ){
        if ($super) $super($this);
    }

    private function state($suffix){
        $stateName = widgetstate::getChildName(static::class, $suffix);
    } 


    function __parent(){
        widgetstate::reg($this);
        widgetstate::applyDefaultToState($this);
    }

    private function init(){

    }

    function setName($name){
        if ($this->name==false)
            $this->name = $name;
    }

    function getName(){
        return $this->name;
    }

    protected function set($key, $value){
        $this->data[$key] = $value;
    }

    protected function get($key){
        if (isset($this->data[$key])){
            return $this->data[$key];
        } else {
            return false;
        }
    }

    protected function getdefault($key){
        return $this->default[$key];
    }

    public function default(): array {
        return [];
    }

    public function alias(): array {
        return [];
    }


    public function __any($method, $props){
        return new state__method($this->name, $method, $props);
    }

}