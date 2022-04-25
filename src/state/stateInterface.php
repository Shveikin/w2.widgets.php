<?php

namespace Widgets\state;


interface stateinterface {
    const target = '/';


    const upload_type = 1;
        const UPLOAD_DEFAULT_FIRST = 1;
        const UPLOAD_ALIAS_FIRST = 2;


    const data_type = false;
        const DATA_INT = 3;
        const DATA_FLOAT = 4;



    function default(): array;
    function alias(): array|bool;
    function revice($key, $value);
    function onchange();
}