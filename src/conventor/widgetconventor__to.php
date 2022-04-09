<?php

namespace Widgets\conventor;

trait widgetconventor__to {

    protected function toString($element){
        $type = $this->getType($element);
    
        return widgetconventor::convert($type, 'String', $element);
    }

    protected function toHTML($element){
        return widgetconventor::toString($element);
    }   

    protected function toArray($element){
        return is_array($element)?$element:[$element];
    }

}