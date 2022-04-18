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

    protected function WidgetToElement(widget $widget){
        return $widget->toElement();
    }

    protected function ElementToString($element){
        return widgetconventor::toWidget($element)->toHTML();
    }

    protected function StateMethodToElement(state__method $statemethod){
        return $statemethod->toElement();
    }

    protected function ArrayToString(array $array){
        $result = '';
        foreach ($array as $value) {
            $result .= widgetconventor::toString($value);
        }
        return $result;
    }

    protected function RequestToElement($request){
        return $request->apply();
    }

    protected function BoolToString($bool){
        return '';
    }


}