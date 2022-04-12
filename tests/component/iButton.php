<?php

namespace Tests\component;

use Widgets\widget\widget;

class iButton extends widget {
    public $element = 'button';
    function __construct($title){
        $this->innerHTML = $title;
        $this->style = "
            padding: 20px;
        ";

        $this->onclick = $this->sayHello;
    }

    function sayHello(){
        return 'alert(11)';
    }
}
