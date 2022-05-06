<?php

namespace Tests\component;

use Widgets\widget\c;
use Widgets\widget\widget;

class HideBox extends widget {

    function __construct($secret){
        $this->child = c::button('show', onclick: $this->state->_toggle('open'));
        $this->child = $this->state->watchif('open', true, $secret);

        if ($secret=='hello'){
            $this->state->set('open', true);
        }
    }

}