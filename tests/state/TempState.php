<?php

namespace Tests\states;

use Widgets\state\state;

class TempState extends state {

    public function default(): array {
    return ['unit' => 'F'];
    }

}