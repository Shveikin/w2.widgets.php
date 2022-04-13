<?php

namespace DI2;

class DependencyWrap {
    private $element = false;

    function __construct($class){
        $this->class = $class;
    }

    function __init(){
        if ($this->element == false) 
            $this->element = ContainerManager::container()->class($this->class);
    }

    function __call($func, $args){
        $this->__init();
        return $this->element->{$func}(...$args);
    }

    function __get($key){
        $this->__init();
        return $this->element->{$key};
    }

    function __set($key, $value){
        $this->__init();
        $this->element->{$key} = $value;
    }
}

class ContainerManager {
    static $container = false;
    private $containers = [];
    private $reg = [];

    static function container(){
        if (self::$container==false)
            self::$container = new self();

        return self::$container;
    }

    function class($class){
        if (!isset($this->reg[$class])){
            if (!isset($this->containers[$class])){
                $this->reg[$class] = true;

                $tempElement = new $class(function($el){
                    $class = get_class($el);
                    $this->containers[$class] = $el;

                    if ($el->dependency)
                    foreach($el->dependency as $val => $className){
                        $el->{$val} = new DependencyWrap($className);
                    }
                });

                if (method_exists($tempElement, '__parent')){
                    $tempElement->__parent();
                }

                if (!isset($this->containers[$class])){
                    $this->containers[$class] = $tempElement;
                }
            }
        } else {
            if (!isset($this->containers[$class]))
                throw new \Exception(" $class не реальзует функцию \$super(\$this) в __construct ", 1);
        }

        return $this->containers[$class];
    }
}

trait Container {
    static function main(){
        return ContainerManager::container()->class(static::class);
    }

    static function __callStatic($func, $arguments){
        $class = ContainerManager::container()->class(static::class);
        return $class->__apply($func, $arguments);
    }

    function __apply($func, $arguments){
        if (method_exists($this, $func)){
            $method = new \ReflectionMethod($this, $func);
            $parameters = $method->getParameters();

            $method->setAccessible(true);
            return $method->invoke($this, ...$arguments);
        } else {
            return $this->__any($func, $arguments);
        }
    }

    function __call($func, $arguments){
        return $this->__apply($func, $arguments);
    }

    function __any($func, $arguments){
        throw new \Exception(get_class($this) . " method $func - отсутствует ");
    }
}