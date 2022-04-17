<?php

namespace Widgets\conventor;

use Widgets\state\state;
use Widgets\state\state__method;
use Widgets\widget\widget;

use DI2\Container;
use Widgets\widget\tools\request;

class widgetconventor {
    use Container;
    use widgetconventor__methods;
    use widgetconventor__to;
    use widgetconventor__fromTo;

    private $singletags = [
		'area' => false,
		'base' => false,
		'br' => false,
		'col' => false,
		'embed' => false,
		'hr' => false,
		'img' => 'src',
		'input' => 'value',
		'textarea' => 'value',
		'link' => 'href',
		'menuitem' => false,
		'meta' => false,
		'param' => false,
		'source' => false,
		'track' => false,
		'wbr' => false,
	];

    static $methods = ['StateMethod', 'WidgetRequest']; 

    private $trims = [
        'style' => " \n"
    ];

    private function getTrimFor($prop){
        if (isset($this->trims[$prop])) {
            return $this->trims[$prop];
        } else {
            return false;
        }
    }

    protected function checkSingleTag($tag){
        if (isset($this->singletags[$tag])){
            return $this->singletags[$tag];
        } else {
            return false;
        }
    }

/* 
    static $singletags = [
        'input' => 'value'
    ]; 
*/

    static $opentag = '<';
    static $closetag = '>';




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
        if ($element instanceof request){
            return 'Request';
        } else
        if (is_string($element)){
            return 'String';
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

    protected function convert($from, $to, $element){
        if ($from == $to)
            return $element;

        $func = "{$from}To{$to}";

        $result = $this->{$func}($element);
        $type = $this->getType($result);
        if ($type==$to){
            return $result;
        } else {
            return widgetconventor::convert($type, $to, $result);
        }
    }
}