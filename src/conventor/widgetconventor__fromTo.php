<?php

namespace Widgets\conventor;

use Widgets\state\state__method;
use Widgets\widget\widget;

trait widgetconventor__fromTo {

    protected function WidgetToString(widget $widget): string {
        return $widget;
    }

    protected function StateMethodToString(state__method $statemethod): string {
        return $statemethod->toHTML();
    }

}