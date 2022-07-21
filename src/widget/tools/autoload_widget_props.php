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
        $className = get_class($this);
        if (state::$__loadStateFromRequestStorage__){
            if ($state = requeststorage::getLocalStateFor($className)){
                return $state;
            }
        }


        $stateName = 'localstate_' . spl_object_id($this);
        array_push($this->useState, [$className, $stateName]);
        return state::name($stateName);
    }
}