<?php

namespace Tests\states;
use Widgets\state\state;

class TempState extends state {
    // const type = 'int';

    function alias(): array|bool {
        return ['_buy' => 'buy'];
    }
}