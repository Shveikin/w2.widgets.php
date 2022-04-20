<?php

namespace Widgets\request;


use Widgets\conventor\widgetconventor;

class staterequest {
    private $list = [];
    public $stateName;

    function __construct($stateName){
        $this->stateName = $stateName;
        RequestController::reg($this);
    }

    function __set($requestName, $request){
        $this->list[$requestName] = $request;
    }

    function toElement(){
        return [
            'element' => 'widgetrequest',
            'props' => [
                'list' => widgetconventor::toElement($this->list)
            ]
        ];
    }
}