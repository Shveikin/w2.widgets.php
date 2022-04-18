<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\widget\widget;


class iButton extends widget {
    public $element = 'button';
    public $useState = [
        [TempState::class, 'Rashod']
    ];

    function __construct($title, $do){
        $this->innerHTML = $title;
        $this->style = "
            padding: 2px;
        ";

        $this->onclick = $this->ctemp;
    }

    function ctemp(){
        TempState::name('Rashod')->set('unit', 'R');
    }

}