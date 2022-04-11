<?php

namespace Tests\component;
use Widgets\widget\widget;


class Table extends widget {

    private function draw($title){

        $this->layout->add('Hello'); 
    }

}