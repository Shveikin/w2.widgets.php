<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\widget\widget;


class iButton extends widget {
    public $element = 'div';

    function __construct($title, $do){
        $this->innerHTML = $title;
        $this->style = "
            padding: 10px;
            border: 1px solid #eee;
            margin-top: -1px;
        ";

        $this->onclick = $this->ctemp;
    }

    function ctemp(){
        TempState::name('Rashod')->_set('unit', 'R');
    }

}