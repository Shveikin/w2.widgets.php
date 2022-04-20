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

    function __construct($title, $do){
        $this->innerHTML = $title;
        $this->style = "
            padding: 2px;
        ";

        $this->onclick = [
                            state::_set('phrase', 'eeee'),
                            $this->ctemp
                        ];
    }

    function ctemp(){
        // state::set('phrase', 'HEEELLOOYY');
        widgetdialog::show('Hello');
    }

}