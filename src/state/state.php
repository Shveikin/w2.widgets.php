<?php

namespace Widgets\state;

use DI2\Container;
use Widgets\request\requeststorage;

class state extends widgetstate__tools {
    use Container;

    const upload_type = false;

    private $data = false;
    private $default = [];
    private $alias = false;
    public $delay = false;


    private $name = false;
    private $initialization = false;

    private $childs = [];

    function __construct($super, $name = false){
        $super($this);

        widgetstate::reg($this, $name);

        if (static::upload_type==state::UPLOAD_ALIAS_FIRST){
            if (method_exists($this, 'alias')){
                $this->alias = $this->alias();
            }
        }


        $dafault = [];
        if (method_exists($this, $this->name)){
            $dafault = $this->{$this->name}();
        } else
        if (method_exists($this, 'default')){
            $dafault = $this->default();
        }
        $this->default = $dafault;
        $this->setdata($dafault, 'create');

        if (method_exists($this, 'alias')){
            $this->alias = $this->alias();
        }

        $this->readget();

        $this->onchange = $this->onchange();
        $post = requeststorage::getBySource($this->getSource());
        if ($post){
            $this->setdata($post, 'request');
        }
        $this->initialization = true;
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
        $lvalue = $this->get($key);
        if ($lvalue!==$value) {
            $this->data[$key] = $value;

            if ($this->initialization) widgetstate::changedState($this->getSource());
            

            $revice_block = isset($this->__revice_block__[$key])?$this->__revice_block__[$key]:false;
            if (!$revice_block){
                $this->__revice_block__[$key] = true;
                $this->revice($key, $value);
                $this->__revice_block__[$key] = false;
            }
        }
    }



    protected function get(string|int $key){
        if (isset($this->data[$key])){
            return $this->data[$key];
        } else {
            if (str_starts_with($key, '_')){
                return [];
            } else {
                return false;
            }
        }
    }

    protected function getdefault(string|int $key){
        if (isset($this->default[$key])){
            return $this->default[$key];
        } else {
            return str_starts_with($key, '_')?[]:false;
        }
    }

    protected function setdefault(string|int $key) {
        $this->set($key, $this->getdefault($key));
    }

    protected function isdefault(string|int $key) {
        return $this->getdefault($key) == $this->get($key);
    }

    protected function setdata(array $data, $form  = 'none'){
        foreach ($data as $key => $value) {
            // $updates = $form=='request' && !str_starts_with($key, '__');
            
            $this->set($key, $value);
        }
    }


    protected function export($val){
        $result = $this->{$val};
        return $result;
    }





    public function __any($method, $props){
        return new state__method($this->name, $method, $props);
    }

    public function getdata(): array {
        return is_array($this->data)?$this->data:[];
    } 

    public function getdefaults(){
        return $this->default;
    } 

    public function getalias(){
        return $this->alias;
    } 

    protected function appendto($key, $childkey, $value){
        if (!isset($this->data[$key]) || !is_array($this->data[$key])){
            $this->data[$key] = [];
        }

        $this->data[$key][$childkey] = $value;
    }


    protected function getSource(){
        return [static::class, $this->name];
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

}