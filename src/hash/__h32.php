<?php

namespace Widgets\hash;

use Widgets\conventor\widgetconventor;
use Widgets\widget\widget;

class __h32 extends widget {
    public $element = '__h32';
    const vars = ['map', 'stack'];

    private $map = [
        'div' => 'q',
        'element' => 'w',
        'props' => 'e',
        'child' => 'r',
        'method' => 't',
        'stateName' => 'u',
        'modelin' => 'i',
        'value' => 'o',
        'label' => 'p',
        'args' => 'a',
        'style' => 's',
        'input' => 'd',
        'checked' => 'f',
        'h1' => 'g',
        'h2' => 'h',
        'h3' => 'j',
        'h4' => 'k',
        'h5' => 'l',
        'h6' => 'z',
        'textarea' => 'x',
        'span' => 'c',
        'watch' => 'y',
        'watchif' => 'v',
        'watchin' => 'b',
        'watchdefault' => 'n',
        'StateMethod' => 'm',
        'type' => 'qq',
        'json' => 'qw',
        'button' => 'qe',
        'onclick' => 'qr',
        'innerText' => 'qt',
    ];

    private $stack = []; 


    function __construct($widget, $toLang = false){
        $this->method = 32;
        $this->base = $this->hash(is_array($widget)?$widget:widgetconventor::toElement($widget));
        $this->list = $this->translate($toLang);
    }

    function translate($translate){
        if ($translate){

        } else {
            return array_keys($this->stack);
        }
    }

    function hashItm($value){
        $type = widgetconventor::getType($value);
        switch($type){
            case 'Bool':
            case 'None':
                return $value?'+':'-';
            break;
            case 'String':
            case 'Int':

            break;
            case 'Array':
                return $this->hash($value);
            break;
            case 'StateMethod':
            case 'Widget':
            case 'Element':
            case 'StateMapMethod':
                return $this->hash(widgetconventor::toElement($value));
            break;
            default:
                die("Не знаю как хешировать $type");
            break;
        }

        if (is_bool($value)) return $value;

        if (isset($this->map[$value]))
            return $this->map[$value];

        if (!isset($this->stack[$value]))
            $this->stack[$value] = count($this->stack);

        return $this->stack[$value]; 
    }

    function hash($element){
        $result = [];

        if (!widgetconventor::isAssoc($element)){
            foreach ($element as $value) {
                $valueid = $this->hashItm($value);

                $result[] = $valueid;
            }
        } else {
            foreach($element as $key => $value) {
                $ketid = $this->hashItm($key);
                $valueid = $this->hashItm($value);
                
                $result[$ketid] = $valueid;
            }
        }

        return $result;
    }

    function hash64(){
        $this->method = 64;
        $this->base = str_replace('"', '', json_encode($this->base));
        return $this;
    }
}