<?php

namespace Widgets\conventor;

use Widgets\widget\c;

trait widgetconventor__to {

    protected function toString($element){
        $type = $this->getType($element);
    
        return widgetconventor::convert($type, 'String', $element);
    }

    protected function toHTML($element){
        return '<div>' . widgetconventor::toString($element) . '</div>';
    }   

    protected function toArray($element){
        return is_array($element)?$element:[$element];
    }

    private function toWidget($element){
        return c::div($element);
    }

}