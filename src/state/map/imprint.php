<?php

namespace Widgets\state\map;



class imprint {
    
    private $props = [];

    function __get($key){
        $keyHash = md5($key);
        $this->props[$key] = $keyHash;
        return "**$keyHash**";
    }

    function getProps(){
        return $this->props;
    }

}