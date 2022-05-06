<?php

namespace Widgets\conventor;

use Widgets\widget\c;

trait widgetconventor__to {

    protected function toString($element){
        $type = $this->getType($element);
    
        return widgetconventor::convert($type, 'String', $element);
    }

    protected function toHTML($element){
        $type = $this->getType($element);
        $result = '';
        switch ($type) {
            case 'Bool':
            case 'Request':
                return '';
            break;
            case 'String':
            case 'Int':
                return $element;
            break;
            case 'Array':
                foreach ($element as $value) {
                    $result .= '<div>'. widgetconventor::toHTML($value) .'</div>';
                }
            break;
            case 'Widget':
                return $element->toHTML();
            break;
            case 'StateMethod':
                return '<!-- StateMethod /-->';
            break;
            case 'StateMapMethod':
                return '<!-- StateMapMethod /-->';
            break;
            default:
                die("не знаю как перевести $type в HTML");
            break;
        }

        return $result;
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
            case 'Int':
            case 'String':
            case 'Bool':
            // case 'Array':
                return $element;
            break;
            case 'Array':
                return widgetconventor::ArrayToElement($element);
            break;
            default:
                return widgetconventor::convert($type, 'Element', $element);
            break;
        }
    }


}