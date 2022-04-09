<?php

namespace Widgets\widget;

class c {

    static function __callStatic($tag, $props){
        return new widget($tag, $props);
    }

}