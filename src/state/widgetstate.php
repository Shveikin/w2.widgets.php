<?php


namespace Widgets\state;

use DI2\Container;
use Exception;
use Reflection;
use ReflectionClass;

class widgetstate {
    use Container;

    private $global = [];
    private $hash = [];

    private function name($stateName){
        if (isset($this->global[$stateName])){
            return $this->global[$stateName];
        } else {
            // $stateName
        }
    }


    private function reg(state $state){
        $stateName = (new \ReflectionClass($state))->getShortName();

        if (isset($this->global[$stateName])){
            if (!isset($this->hash[$stateName])){
                $this->hash[$stateName] = 1;
            }

            $stateName .= '_' . $this->hash[$stateName]++;
        }

        $state->setName($stateName);
        $this->global[$stateName] = $state;
    }


        
    private function create(...$props){
        $name = $props['name'];

        $classFrom = state::class;
        if (isset($props['from'])) {
            if (!is_subclass_of($props['from'], state::class)){
                throw new Exception("$props[from] class должен наследоваться от state");
            }
            $classFrom = $props['from'];
        }

        $stateName = widgetstate::getChildName($classFrom, $name);

        $default = isset($props['default'])?$props['defalut']:false;

        $this->global[$stateName] = new $classFrom(default: $default);
        return $this->global[$stateName];
    }

    static function getChildName($classParrent, $suffix){
        $parentClassName = (new \ReflectionClass(static::class))->getShortName();
        return $parentClassName . $suffix;
    }

    private function global(){
        return $this->global;
    }

}