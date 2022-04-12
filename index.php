<?php

require  "./vendor/autoload.php";
use Tests\states\TempState;
use Widgets\state\widgetstate;
use Widgets\widget\c;
use Tests\component\Table;


widgetstate::create(name: 'Rashod', from: TempState::class, default: ['min' => 12]);

echo TempState::state('Rashod')->get('min');

// $wgt = Table::element();

// echo $wgt;