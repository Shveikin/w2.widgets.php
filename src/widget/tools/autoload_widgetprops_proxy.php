<?php

namespace Widgets\widget\tools;

use DI2\DI2;

trait autoload_widgetprops_proxy {
    use DI2;

    function layout(){
        return new widgetprops_proxy($this);
    }
}