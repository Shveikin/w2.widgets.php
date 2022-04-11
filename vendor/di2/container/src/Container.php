<?php

namespace DI2;

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