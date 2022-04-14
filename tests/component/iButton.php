<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\widget\widget;


class iButton extends widget {
    public $element = 'button';
    function __construct($title){
        $this->innerHTML = $title;
        $this->style = "
            padding: 20px;
        ";

        $this->onclick = TempState::name('Rashod')->wrap_set('unit', 'pldx');
    }


}