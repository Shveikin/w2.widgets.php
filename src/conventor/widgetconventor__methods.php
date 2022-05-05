<?php

namespace Widgets\conventor;

use Widgets\state\state;
use Widgets\state\state__method;
use Widgets\widget\widget;
use Widgets\request\widgetrequest;



trait widgetconventor__methods {


    protected function getType($element){
        if ($element instanceof widget){
            return 'Widget';
        } else 
        if ($element instanceof state){
            return 'State';
        } else 
        if (is_array($element)){
            if (isset($element['element'])){
                return "Element";
            }
            return 'Array';
        } else 
        if ($element instanceof state__method){
            return 'StateMethod';
        } else
        if ($element instanceof widgetrequest){
            return 'Request';
        } else
        if (is_string($element)){
            return 'String';
        } else 
        if (is_callable($element)){
            return 'Func';
        } else
        if (is_numeric($element)){
            return 'Int';
        } else 
        if (is_bool($element)){
            return 'Bool';
        } else {
            return 'None';
        }
    }




    /** 
     * Могу конвентировать в Element
    */
    protected function canConvertToElement($element): bool {
        $type = $this->getType($element);
        return in_array($type, ['Widget', 'State', 'StateMethod', 'Request']);
    }



    protected function isAssoc(array $array){
        return !empty(preg_grep('/[^0-9]/', array_keys($array)));
    }
}