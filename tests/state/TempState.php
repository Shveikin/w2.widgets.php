<?php

namespace Tests\states;
use Widgets\state\state;

class TempState extends state {
    const upload_type = state::UPLOAD_ALIAS_FIRST;
    const data_type = state::DATA_INT;

    function alias(): array|bool {
        return ['_buy' => 'buy'];
    }
}