<?php

namespace Tests\component;

use Tests\states\TempState;
use Widgets\state\state;
use Widgets\widget\c;
use Widgets\widget\widget;

class Label extends widget {
    function __construct($title, $value, $stateName = 'list'){
        $this->child = c::label([
            c::input(
                type: 'checkbox',
                value: $value,
                checked: state::name($stateName)->modelin('_values', $value)
            ),
            ' - ',
            $title,
        ],
        style: 'display: flex; padding: 5px;');
    }
}