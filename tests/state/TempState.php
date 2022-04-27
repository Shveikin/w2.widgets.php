<?php

namespace Tests\states;

use Tests\component\iButton;
use Widgets\state\state;

class TempState extends state {
    const upload_type = state::UPLOAD_ALIAS_FIRST;
    const data_type = state::DATA_INT;

    public $delay = 1000;

    function alias(): array|bool {
        return ['_buy' => 'buy'];
    }

    function onchange(){
        return [
            $this->_set('_buy', '1111'),
            iButton::main()->ctemp->bind('HELLO')
        ];
    }
}