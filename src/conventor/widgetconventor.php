<?php

namespace Widgets\conventor;


use DI2\Container;

class widgetconventor extends widgetconventor__hash {
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