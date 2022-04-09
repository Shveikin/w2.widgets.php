<?php

namespace Tests\states;

use Widgets\state\state;

class TempState extends state {

    function __construct(){
        $this->default(
            unit: 'F'
        );

        $this->alias(
            unit: 'unit'
        );
    }

}