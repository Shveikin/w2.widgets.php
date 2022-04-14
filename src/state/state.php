<?php

namespace Widgets\state;

use DI2\Container;

class state {
    use Container;

    private $data = false;
    private $default = [];
    private $alias = false;

    private $name = false;

    private $childs = [];

    function __construct($name = false){

        widgetstate::reg($this, $name);

        if (method_exists($this, 'alias')){
            $this->alias = $this->alias();
        }

        $preload = widgetrequest::fillState($this);

        $dafault = [];
        if (method_exists($this, $this->name)){
            $dafault = $this->{$this->name}($preload);
        } else
        if (method_exists($this, 'default')){
            $dafault = $this->default($preload);
        }
        $this->default = $dafault;
        $this->data = $dafault;



        if (method_exists($this, 'alias')){
            $this->alias = $this->alias();
        }

        $postload = widgetrequest::fillState($this);
        foreach ($postload as $statekey => $value) {
            $this->set($statekey, $value);
        }
    }


    private function name($name){
        return widgetstate::name($name, get_class($this));
    }

/* 
    function __parent(){
        
    } 
*/

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

    public function default($preload): array {
        return [];
    }

    public function alias() {
        return false;
    }

    public function getAliasList(): array {
        $result = [];
        if (is_array($this->alias)){
            $result = $this->alias;
        } else {
            if ($this->alias==true && is_array($this->data)) {
                $defaultkeys = array_keys($this->data);
                foreach ($defaultkeys as $statekey) {
                    $result[trim($statekey, '_')] = $statekey;
                }
            } else {
                $result = [];
            }
        }

        return $result;
    }


    public function __any($method, $props){
        return new state__method($this->name, $method, $props);
    }

}