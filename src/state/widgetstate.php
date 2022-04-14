<?php


namespace Widgets\state;

use DI2\Container;
use DI2\MP;
use Exception;



class widgetstate {
    use Container;

    private $global = [];
    private $hash = [];
    private $post = [];


    function __construct($super){
        $super($this);
    }


    private function name($stateName, $source = false){
        if (!isset($this->global[$stateName])){
            $state = MP::GET(
                class: $source?$source:state::class, 
                alias: $stateName,
                constructor: $stateName
            );
            $this->global[$stateName] = $state;
        }

        return $this->global[$stateName];
    }


    private function reg(state $state, $customName = false){
        $stateName = '___';
        if ($customName==false){
            $stateName = (new \ReflectionClass($state))->getShortName();

            if (isset($this->global[$stateName])){
                if (!isset($this->hash[$stateName])){
                    $this->hash[$stateName] = 1;
                }

                $stateName .= '_' . $this->hash[$stateName]++;
            }
        } else {
            $stateName = $customName;
        }
        $this->global[$stateName] = $state;
        $state->setName($stateName);
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