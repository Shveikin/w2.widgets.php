<?php


namespace Widgets\widget\tools;

use Widgets\conventor\widgetconventor;

trait widget__element {
    public function toElement(){
        $result = [
            'element' => $this->element,
            'child' => [],
        ];

        foreach($this->props as $prop => $val){
            $result[$prop] = $val;
        }

        foreach($this->child as $child){
            array_push($result['child'], widgetconventor::toElement($child));
        }

        return $result;
    }
}