<?php

namespace Widgets\widget\tools;

use Widgets\conventor\widgetconventor;

trait widget__html {
    
    private function renderProps() {
        $result = '';
        foreach($this->props as $attr => $value) {
            $result .= " $attr='$value'";
        }
        
        return $result;
    }

    private function renderChilds(){
        $result = '';

        foreach($this->child as $child){
            $result .= widgetconventor::toHTML($child);
        }

        return $result;
    }

    private function closeTag(){
        if (!isset(widgetconventor::$singletags[$this->element])){
            return widgetconventor::$opentag . '/' .
                    $this->element .
                    widgetconventor::$closetag;
        } else {
            return '';
        }
    }

}