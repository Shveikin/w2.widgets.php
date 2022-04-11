<?php


namespace Widgets\state;

use Widgets\tools\Container;

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

    private function global(){
        return $this->global;
    }

}