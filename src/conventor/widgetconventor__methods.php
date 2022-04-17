<?php

namespace Widgets\conventor;

use Widgets\widget\c;

trait widgetconventor__methods {

    /** 
     * Могу конвентировать в Element
    */
    protected function canConvertToElement($element): bool {
        $type = $this->getType($element);
        return in_array($type, ['Widget', 'State', 'StateMethod', 'Request']);
    }
}