<?php

namespace Widgets\state;

use DI2\Container;
use Widgets\widget\tools\widgetrequest;

class state {
    use Container;

    private $data = false;
    private $default = [];
    private $alias = false;

    private $name = false;

    private $childs = [];

    function __construct($super, $name = false){
        $super($this);

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

    private $__revice_block__ = [];
    protected function set(string|int $key, $value){
        $this->data[$key] = $value;

        $revice_block = isset($this->__revice_block__[$key])?$this->__revice_block__[$key]:false;
        if (!$revice_block){
            $this->__revice_block__[$key] = true;
            $this->revice($key, $value);
            $this->__revice_block__[$key] = false;
        }
    }

    public function revice($key, $value){

    }

    protected function setFromRequest(array $data){

    }

    protected function get(string|int $key){
        if (isset($this->data[$key])){
            return $this->data[$key];
        } else {
            return false;
        }
    }

    protected function getdefault(string|int $key){
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

    public function getdata(){
        return $this->data;
    } 

    public function getdefaults(){
        return $this->default;
    } 

    public function getalias(){
        return $this->alias;
    } 

    protected function appendto($key, $childkey, $value){
        if (!is_array($this->data[$key])){
            $this->data[$key] = [];
        }

        $this->data[$key][$childkey] = $value;
    }

/*     
    protected function state(){
        $exception = ["Widgets\state\state", "DI2\MP", 'ReflectionMethod'];
        $ex = new \ErrorException('test', 0, 56, __FILE__, __LINE__);
        foreach ($ex->gettrace() as $line) {
            if (isset($line['class']))
            if (!in_array($line['class'], $exception)){
                var_dump($line);
            }
        }
        die();
    } 
*/

}