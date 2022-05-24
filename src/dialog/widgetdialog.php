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
        'hidetitle' => 'hidetitle',
        'width' => 'width',
        'height' => 'height',
    ];

    static function show(...$props){

        foreach ($props as $key => $value) {
            if (isset(self::$props[$key])) {
                $stateKey = self::$props[$key];
                dialogstate::set($stateKey, $value);
            }
        }

    }


    static function _show(...$props){
        $rules = [];

        foreach ($props as $key => $value) {
            if (isset(self::$props[$key])) {
                $stateKey = self::$props[$key];
                $rules[] = dialogstate::_set($stateKey, $value);
            }
        }

        return $rules;
    }


}