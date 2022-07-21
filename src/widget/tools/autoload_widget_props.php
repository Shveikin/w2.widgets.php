<?php

namespace Widgets\widget\tools;

use DI2\DI2;
use Widgets\request\requeststorage;
use Widgets\state\state;

trait autoload_widget_props {
    use DI2;

    function _layout(){
        return new widget_layout($this);
    }

    function _state(){
        $state = false;
        $className = get_class($this);
        if (state::$__loadStateFromRequestStorage__){
            if ($isState = requeststorage::getLocalStateFor($className)){
                $state = &$isState;
            }
        }



        if (!$state){
            $stateName = 'localstate_' . spl_object_id($this);
            $state = state::name($stateName);
        }




        $this->useState([$className, $state->getName()]);
        return $state;
    }
}