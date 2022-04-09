<?php

require  "./vendor/autoload.php";
use Tests\states\TempState;
use Widgets\widget\c;

TempState::set('val', '7888');

echo c::textarea(TempState::watch('val'));
