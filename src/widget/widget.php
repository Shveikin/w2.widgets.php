<?php

namespace Widgets\widget;

use JsonSerializable;
use Widgets\conventor\widgetconventor;
use Widgets\request\requeststorage;
use Widgets\widget\tools\widget__element;
use Widgets\widget\tools\widget__html;

class widget implements JsonSerializable {
    use widget__html;
    use widget__element;

    const vars = [];

    public $url = '/';
    public $element = 'div';
    private $props = [];
    private $child = [];
    private $bind = false;

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
        } else 
        if (in_array($key, static::vars)) {
            $this->{$key} = $value;
        } else {
            $this->props[$key] = $value;
        }
    }

    function __get($key){
        if (in_array($key, static::vars)) {
            return $this->{$key};
        } else {
            return requeststorage::get(
                source: get_class($this),
                method: $key,
                url: $this->url,
                useState: $this->useState
            );
        }
    }

    function __toString(){
        return $this->toHTML();
    }

    static function c(...$props){
        return new (static::class)(...$props);
    }

    public function jsonSerialize(){
        return $this->toElement(true);
    }
}
