<?php

namespace Widgets\state;

use DI2\Container;

class widgetrequest {
    use Container;

    private $post = []; 
    private $get = []; 

    function __construct($super){
        $super($this);
        $data = file_get_contents('php://input');
        if ($data){
            $data = json_decode($data);
        }

        $this->get = $_GET;
    }

    private function fillState($state){
        $result = [];

        foreach ($state->getAliasList() as $url => $statekey) {
            if (isset($this->get[$url])){
                $result[$statekey] =$this->get[$url];
            }
        }

        return $result;
    } 
}