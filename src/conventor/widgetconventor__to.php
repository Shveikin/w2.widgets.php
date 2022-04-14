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

    private function toElement($element){
        $type = $this->getType($element);

        switch ($type) {
            case 'String':
                return $element;
            break;
            default:
                return widgetconventor::convert($type, 'Element', $element);
            break;
        }
    }

    private function toClearElement($element){
        $type = $this->getType($element);

        switch ($type) {
            case 'String':
                return $element;
            break;
            default:
                $element = widgetconventor::convert($type, 'Element', $element);
            break;
        }

        if ($element['element']=='div'){
            unset($element['element']);
        }

        if (isset($element['props']))
        if (isset($element['props']['child']))
        if (count($element['props'])==1){
            $element = $element['props']['child'];
            if (count($element)==1){
                $element = $element[0];
            }
        }

        return $element;
    }

}