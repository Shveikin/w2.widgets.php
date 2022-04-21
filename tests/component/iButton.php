<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\dialog\widgetdialog;
use Widgets\state\state;
use Widgets\widget\widget;


class iButton extends widget {
    public $element = 'button';
    public $useState = [
        [TempState::class, 'Rashod']
    ];

    function __construct($title, $setTitle){
        $this->innerHTML = $title;
        $this->style = "
            padding: 2px;
        ";

        $this->onclick = $this->ctemp->apply(setTitle: $setTitle);
    }

    function ctemp(){
        
        $log = $this->bind;

        showDialog('Ну хелло');

    }

}