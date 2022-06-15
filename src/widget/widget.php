<?php

namespace Widgets\widget;

use JsonSerializable;
use Widgets\conventor\widgetconventor;
use Widgets\request\requeststorage;
use Widgets\state\state;
use Widgets\widget\tools\autoload_widgetprops_proxy;
use Widgets\widget\tools\widget__element;
use Widgets\widget\tools\widget__html;
// use Widgets\widget\tools\widgetprops_proxy;

class widget implements JsonSerializable {
    use autoload_widgetprops_proxy;
    use widget__html;
    use widget__element;



    const vars = [];

    
    public $url = '/';
    public $element = 'div';
    public $props = [];
    private $child = [];
    private $bind = false;
    private $innerHTML = false;
    
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
            if (in_array($attr, [0, 'innerText'])){
                $child = $val;
            } else if ($attr == 'innerHTML'){
                $this->layout->innerHTML = $val;
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

    // function __set($key, $value){
    //     if ($key=='child') {
    //         array_push($this->child, $value);
    //     } else 
    //     if (in_array($key, static::vars) || $key=='layout') {
    //         $this->{$key} = $value;
    //     } else {
    //         $this->props[$key] = $value;
    //     }
    // }

    function __anyKey($key){
        // return $this->__to($key);

        if ($key=='state'){
            return state::name('localstate_' . spl_object_id($this));
        } else
        if (method_exists($this, $key)){
            return requeststorage::get(
                source: get_class($this),
                method: $key,
                url: $this->url,
                useState: $this->useState
            );
        }
    }

    // function __get($key){
    //     return $this->__to($key);
    // }


    // function __to($key){
    //     if ($key=='child')
    //         return $this->child;
    //     else
    //     if ($key=='state'){
    //         return state::name('localstate_' . spl_object_id($this));
    //     } else
    //     if (method_exists($this, $key)){
    //         return requeststorage::get(
    //             source: get_class($this),
    //             method: $key,
    //             url: $this->url,
    //             useState: $this->useState
    //         );
    //     } else 
    //     if ($this->__is_var($key) || $key == 'layout') {
    //         return $this->{$key};
    //     } else {
    //         return $this->props[$key];
    //     }
    // }

    function bindAliasToMethod($methodName, $methodAlias = false){
        requeststorage::setAlias(
            $methodAlias?$methodAlias:$methodName, 
            requeststorage::getHash(
                get_class($this),
                $methodName,
                $this->url
            )
        );
    }

    static private $__vars__ = false;
    function __is_var($key){
        if (static::$__vars__===false){
            static::$__vars__ = array_count_values(static::vars);
        }
        return isset(static::$__vars__[$key]);
    }

    function __toString(){
        return $this->toHTML();
    }

    static function c(...$props){
        $class = static::class;

        $construct = [];

        if (!empty($props)){
            $parameters = (new \ReflectionClass($class))->getConstructor()?->getParameters();
            if (!empty($parameters)){
                $count = 0;
                foreach ($parameters as $prop) {
                    if (isset($props[$prop->name])){
                        $construct[$prop->name] = $props[$prop->name];
                        unset($props[$prop->name]);
                    } else if (isset($props[$count])){
                        $construct[$count] = $props[$count];
                        unset($props[$count]);
                    }
                    $count++;
                }
            }
        }


        $component = new ($class)(...$construct);

        foreach ($props as $attr => $value) {
            $component->{$attr} = $value;
        }

        return $component;
    }

    public function jsonSerialize(){
        return $this->toElement(true);
    }
}
