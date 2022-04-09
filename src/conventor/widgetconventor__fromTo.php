<?php

namespace Widgets\conventor;

use Widgets\widget\widget;

trait widgetconventor__fromTo {

    protected function WidgetToString(widget $widget): string {
        return $widget;
    }

    protected function StateMethodToString($statemethod): string {
        return $statemethod;
    }

}