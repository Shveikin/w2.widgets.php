<?php

namespace Widgets\state;


interface stateinterface {

    function default(array $preload): array;
    function alias(): array|bool;
    function revice($key, $value);
    function onchange();

}