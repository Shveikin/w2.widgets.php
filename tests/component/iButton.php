<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\widget\widget;


class iButton extends widget {
    public $element = 'button';

    function __construct($title, $do){
        $this->innerHTML = $title;
        $this->style = "
            padding: 20px;
        ";

        $this->onclick = TempState::name('Rashod')->_set('unit', $do);
    }

}