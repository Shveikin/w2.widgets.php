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
    ];

    private $stack = []; 


    function __construct($widget){
        $hash = $this->hash(widgetconventor::toElement($widget));

        $this->list = array_keys($this->stack);
        $this->hash = $hash;
    }

    function hashItm($value){
        $type = widgetconventor::getType($value);
        switch($type){
            case 'Bool':
                return $value;
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
}