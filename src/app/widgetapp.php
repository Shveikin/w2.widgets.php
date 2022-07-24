<?php

namespace Widgets\app;

use DI2\Container;

class widgetapp extends widgetapp__tools {
    use Container;

    public $title = 'widget app';
    public $lang = 'en';


    const htmlRender = true;
    const structure = false;
    const cigaretteBurn = true;
    const hashApp = true;

    const onlyComplection = false;


    const script = true;
        const SCRIPT_CONNECTED_MANUALLY = 32;
        const SCRIPT_CONNECTED_AUTOMATICALLY = 33;


}