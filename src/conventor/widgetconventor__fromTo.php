<?php

namespace Widgets\conventor;

use Widgets\state\state__method;
use Widgets\widget\widget;

trait widgetconventor__fromTo {

    protected function WidgetToString(widget $widget): string {
        return $widget;
    }

    protected function StateMethodToString(state__method $statemethod): string {
        return $statemethod->toHTML();
    }

    protected function ArrayToElement(array $array){
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = widgetconventor::toElement($value);
        }

        return [
            'element' => 'Array',
            'props' => $result
        ];
    } 

}