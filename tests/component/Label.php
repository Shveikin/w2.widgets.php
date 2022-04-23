<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\widget\c;
use Widgets\widget\widget;

class Label extends widget {
    function __construct($title, $value){
        $this->child = c::label([
            c::input(
                type: 'checkbox',
                value: $value,
                checked: TempState::modelin('_buy', $value)
            ),
            ' - ',
            $title,
        ],
        style: 'display: flex; padding: 5px;');
    }
}