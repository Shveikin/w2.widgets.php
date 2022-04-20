<?php

namespace Widgets\state;

use Widgets\request\staterequest;
use Widgets\request\widgetrequest;

interface stateinterface {

    function default(array $preload): array;
    function alias(): array|bool;
    function revice($key, $value);
    function staterequest(staterequest $request);
    function onchange(): staterequest|bool;

}