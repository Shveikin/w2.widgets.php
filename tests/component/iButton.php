<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\widget\widget;


class iButton extends widget {
    public $element = 'button';
    public $useState = [
        [TempState::class, 'Rashod']
    ];

    function __construct($title, $setTitle, $vr = 0){
        $this->innerHTML = $title;
        $this->style = "
            padding: 2px;
        ";

        if ($vr==0)
            $this->onclick = $this->ctemp->bind($setTitle . ' *** ');
        else
            $this->onclick = $this->ccemp->bind($setTitle);
    }

    function ctemp($setTitle){
        showDialog($setTitle);
        sleep(2);
    }

    function ccemp($setTitle){
        showDialog($setTitle, title: 'no delay');
    }

}