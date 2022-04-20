<?php

namespace Widgets\dialog;

use Widgets\state\state;

class dialogstate extends state {

    function default(array $preload): array {
        return [
            '__message' => false,
            'title' => '',
            'enctype' => false,
            'action' => '/',
            'method' => 'POST',
        ];
    }

}