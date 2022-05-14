<?php

namespace Widgets\state;

use DI2\Container;
use Exception;
use Widgets\request\requeststorage;
use Widgets\state\map\StateMapMethod;

class state extends widgetstate__tools {
    use Container;

    const upload_type = false;

    private $data = false;
    private $stack = [];
    private $default = [];
    private $alias = false;
    public $delay = false;


    private $name = false;
    private $initialization = false;
    private $changed = false;
    private $isRendered = false;

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
        
        $post = requeststorage::getBySource($this->getSource());
        if ($post){
            $this->setdata($post, 'request');
        } else {
            $this->setdata($dafault, 'create');
        }

        if (method_exists($this, 'alias')){
            $this->alias = $this->alias();
        }

        $this->readget();

        $this->onchange = $this->onchange();
        

        $this->initialization = true;

        if (!empty($this->stack)){
            $this->stack_init();
        }
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

    protected function set(string|int $key, $value){
        $this->set__($key, $value);
    }

    private $__revice__ = [];
    private function set__(string|int $key, $value, $type = 'auto'){
        $lvalue = $this->get__($key);
        if ($lvalue!==$value) {
            if (!$this->initialization && $type=='auto'){
                if (!isset($this->stack[$type])) $this->stack[$type] = [];
                $this->stack[$type][$key] = $value;
            } else {
                $this->data[$key] = $value;
                if (!$this->changed) {
                    widgetstate::changedState($this->getSource());
                    $this->changed = true;
                }
                $this->isRendered = false;

                $this->__revice__[$key] = true;
                $this->revice($key, $value, $this->initialization);
            }
        }
    }


    private function stack_init(){
        if (isset($this->stack['auto'])){
            $this->setdata($this->stack['auto'], 'stack data');
        }

        $this->stack = [];
    }

    protected function get(string|int $key){
        // if (!$this->initialization)
        //     if (!isset($this->__revice__[$key]) || !$this->__revice__[$key])
        //         throw new \ErrorException("not init $this->name [$key]", 0, 56, __FILE__, __LINE__);
        return $this->get__($key);
    }

    private function get__(string|int $key){
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

    function updateDefaults(){
        foreach ($this->default as $key => $value) {
            $this->default[$key] = $this->get($key);
        }
    }


    protected function isdefault(string|int $key) {
        return $this->getdefault($key) == $this->get($key);
    }

    protected function setdata(array $data, $form  = 'none'){
        foreach ($data as $key => $value) {
            $this->set__($key, $value, $form);
        }
    }


    protected function export($val){
        $result = $this->{$val};
        if (is_callable($result)) {
            $result = $result();
        }
        $this->changed = false;
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
        if (!str_starts_with($key, '_'))
            throw new Exception("$key - [appendto] key должкн быть array"); 
        
        $temp = $this->get($key);
        $temp[$childkey] = $value;

        $this->set($key, $temp);
    }

    protected function map($key, $callback){
        if (!str_starts_with($key, '_'))
            throw new Exception("$key - [Map] key должкн быть array"); 

        return new StateMapMethod($this->name, $key, $callback);
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

    public function isRendered(){
        return $this->isRendered;
    }

    public function setRendered(){
        $this->isRendered = true;
    }

}