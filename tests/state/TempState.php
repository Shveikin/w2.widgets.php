<?php

namespace Tests\states;

use Widgets\request\staterequest;
use Widgets\state\state;

class TempState extends state {

    function default(array $preload): array {
        $result = ['unit' => 'F'];

        return $result;
    }

    function Rashod($preload): array {
        $result = ['unit' => 'hello'];

        return $result;
    }

    function Pressure($preload): array {
        $result = ['unit' => 'pa'];

        return $result;
    }

    function alias(): array|bool
    {
        return ['unit' => 'unit'];
    }


    function revice($key, $value){
        
    }

    function staterequest(staterequest $request)
    {
        $request->filter = [];
    } 
/*     */

}