<?php

namespace Widgets\widget;

use Widgets\conventor\widgetconventor;
use Widgets\widget\tools\widget__element;
use Widgets\widget\tools\widget__html;

class widget {
    use widget__html;
    use widget__element;

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

        if (isset(widgetconventor::$singletags[$this->element])){
            $attrs[widgetconventor::$singletags[$this->element]] = $child;
            $child = [];
        }


        $this->child = widgetconventor::toArray($child);
        $this->props = $attrs;
    }


    function __toString(){
        return $this->toHTML();
    }
}