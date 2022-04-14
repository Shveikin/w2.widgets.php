<?php


namespace Widgets\widget\tools;

use Widgets\conventor\widgetconventor;

trait widget__element {
    public function toElement(){
        $result = [
            'element' => $this->element
        ];

        foreach($this->props as $prop => $val){
            if (!isset($result['props'])) $result['props'] = [];

            $result['props'][$prop] = widgetconventor::toElement($val);
        }

        foreach($this->child as $child){
            if (!isset($result['props'])) $result['props'] = [];
            if (!isset($result['props']['child'])) $result['props']['child'] = [];
            
            array_push($result['props']['child'], widgetconventor::toElement($child));
        }

        return $result;
    }
}