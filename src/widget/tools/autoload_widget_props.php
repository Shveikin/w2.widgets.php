<?php

namespace Widgets\widget\tools;

use DI2\DI2;
use Widgets\state\state;

trait autoload_widget_props {
    use DI2;

    function _layout(){
        return new widget_layout($this);
    }

    function _state(){
        return state::name('localstate_' . spl_object_id($this));
    }
}