<?php

namespace Widgets\widget\tools;

use DI2\DI2;

trait autoload_widget_layout {
    use DI2;

    function _layout(){
        return new widget_layout($this);
    }
}