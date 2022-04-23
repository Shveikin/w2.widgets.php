<?php

namespace Widgets\conventor;
use DI2\Container;

class widgetconventor__hash {

    private $hashList = [];
    private $staticHashList = [
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
        'input'=>'d',
        'checked' => 'f',
        'h1'=> 'g',
        'h2'=> 'h',
        'h3'=> 'j',
        'h4'=> 'k',
        'h5'=> 'l',
        'h6'=> 'z',
        'textarea' => 'x',
        'span' => 'c',
        'watch' => 'y',
        'watchif' => 'v',
        'watchin' => 'b',
        'watchdefault' => 'n',
        'StateMethod' => 'm',
        'type' => 'qq',
    ];
    protected function getHashList(){
        return array_keys($this->hashList);
    }
    
    private function hashItm($value){
        if (isset($this->staticHashList[$value]))
            return $this->staticHashList[$value];

        if (!isset($this->hashList[$value]))
            $this->hashList[$value] = count($this->hashList);

        return $this->hashList[$value]; 
    }


    protected function hashElement($itm){
        $element = widgetconventor::toElement($itm);
        return $this->hash($element);
    }
    
    
    private function hash($element){
        $result = [];

        foreach($element as $key => $value) {
            $ketid = $this->hashItm($key);
            $valueid = is_array($value)
                ?$this->hash($value)
                :$this->hashItm($value);

            $result[$ketid] = $valueid;
        }

        return $result;
    }

}