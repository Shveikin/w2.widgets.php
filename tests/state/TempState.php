<?php

namespace Tests\states;

use Widgets\state\state;

class TempState extends state {

    function default($preload): array {
        $result = ['unit' => 'F'];

        return $result;
    }

    function Rashod($preload): array {
        $result = ['unit' => 'm3h'];

        return $result;
    }

    function Pressure($preload): array {
        $result = ['unit' => 'pa'];

        return $result;
    }

    function alias() {
        return ['unit' => 'unit'];
    }

}