<?php

namespace Widgets\dialog;


class widgetdialog {
    static $style = [];
    static $renderDialogElementToHtml = false;

    static $props = [
        'message' => '__message',
        0 => '__message',

        'title' => 'title',
        'buttons' => '__buttons',

        'enctype' => 'enctype',
        'action' => 'action',
        'method' => 'method',
        'onsubmit' => 'onsubmit',
    ];

    static function show(...$props){

        foreach ($props as $key => $value) {
            if (isset(self::$props[$key])) {
                $stateKey = self::$props[$key];
                dialogstate::set($stateKey, $value);
            }
        }

    }

/*
    static function show__fw(...$props){
        $rules = [];

        foreach (self::$props as $key => $value) {
            if (isset($props[$value])){
                $rules[] = dialogstate::state()->applyTo($key, $props[$value]);
            }
        }

        return state::group($rules);
    }
*/

}