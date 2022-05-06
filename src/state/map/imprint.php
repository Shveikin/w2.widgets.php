<?php

namespace Widgets\state\map;



class imprint {
    
    private $props = [];
    private $math = [];


    function __get($key){
        $keyHash =  '**' . md5($key) . '**';
        $this->props[$key] = $keyHash;
        return $keyHash;
    }

    function getProps(){
        return [
            'keys' => $this->props,
            'math' => $this->math
        ];
    }


    /** 
     * @key - key of item,
     * @length - length of array
    */

    function calc($math){
        $keyHash =  '**' . md5($math) . '**';
        $this->math[$keyHash] = $math;
        return $keyHash;
    }

}