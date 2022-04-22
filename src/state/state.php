<?php

namespace Widgets\state;

use DI2\Container;
use Widgets\request\requeststorage;
use Widgets\request\staterequest;
use Widgets\request\widgetrequest;

/** 
 * 
*/

class state implements stateinterface {
    use Container;

    private $data = false;
    private $default = [];
    private $alias = false;

    private $name = false;

    private $childs = [];
    private $onchange = false;

    function __construct($super, $name = false){
        $super($this);

        

        widgetstate::reg($this, $name);

        if (method_exists($this, 'alias')){
            $this->alias = $this->alias();
        }

        $preload = requeststorage::fill($this);

        $dafault = [];
        if (method_exists($this, $this->name)){
            $dafault = $this->{$this->name}($preload);
        } else
        if (method_exists($this, 'default')){
            $dafault = $this->default($preload);
        }
        $this->default = $dafault;
        $this->setdata($dafault, 'create');
        $this->setdata($preload, 'pre load');

        if (method_exists($this, 'alias')){
            $this->alias = $this->alias();
        }

        $postload = requeststorage::fill($this);
        $this->setdata($postload, 'post load');

        $this->onchange = $this->onchange();
    }

    static function name($name){
        return widgetstate::name($name, static::class);
    }

    function setName($name){
        if ($this->name===false)
            $this->name = $name;
    }

    protected function getName(){
        return $this->name;
    }

    private $__revice_block__ = [];
    protected function set(string|int $key, $value){
        $this->data[$key] = $value;

        widgetstate::changedState($this->getSource());

        $revice_block = isset($this->__revice_block__[$key])?$this->__revice_block__[$key]:false;
        if (!$revice_block){
            $this->__revice_block__[$key] = true;
            $this->revice($key, $value);
            $this->__revice_block__[$key] = false;
        }
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

    protected function setdefault(string|int $key) {
        $this->set($key, $this->getdefault($key));
    }

    protected function setdata(array $data, $form  = 'none'){
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
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


    protected function getSource(){
        return [static::class, $this->name];
    }





    // interface default
    function default(): array {
        return [];
    }

    function alias(): array|bool {
        return false;
    }

    function revice($key, $value){
        
    }

    function onchange(){
        return false;
    }
}