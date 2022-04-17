<?php

namespace Widgets\widget;

use Widgets\conventor\widgetconventor;
use Widgets\widget\tools\request;
use Widgets\widget\tools\widget__element;
use Widgets\widget\tools\widget__html;

class widget {
    use widget__html;
    use widget__element;

    public $url = '/';
    public $element = 'div';
    private $props = [];
    private $child = [];

    public $useState = [];

    function __construct($tag, $props){
        $this->element = $tag;

        $child = isset($props[0])
                ?$props[0]
                :(isset($props['child'])
                    ?$props['child']
                    :[]);

        $attrs = [];

        foreach ($props as $attr => $val) {
            if (in_array($attr, [0,'innerHTML', 'innerText'])){
                $child = $val;
            } else {
                $attrs[$attr] = $val;
            }
        }

        $singleTagValue = widgetconventor::checkSingleTag($this->element);
        $childType = widgetconventor::getType($child);

        if (in_array($childType, ['Array'])){
            $this->child = $child;
        } else {
            if ($singleTagValue){
                $attrs[$singleTagValue] = $child;
            } else {
                $attrs['innerText'] = $child;
            }
            $child = [];
        }

        
        $this->props = $attrs;
    }

    function __set($key, $value){
        if ($key=='child') {
            array_push($this->child, $value);
        } else {
            $this->props[$key] = $value;
        }
    }

    function __get($function_name){
        // $useStates = $this->useState; //$this->getUseStateList();

        return new request(
            function: $function_name,
            url: $this->url,
            class: get_class($this),
            useState: $this->useState
        );

    }

    function __toString(){
        return $this->toHTML();
    }

    static function c(...$props){
        return new (static::class)(...$props);
    }
}
