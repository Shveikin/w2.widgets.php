<?php

namespace Tests\states;
use Widgets\state\state;

class TempState extends state {
    const upload_type = state::alias_first;
    const data_type = state::data_int;

    function alias(): array|bool {
        return ['_buy' => 'buy'];
    }
}