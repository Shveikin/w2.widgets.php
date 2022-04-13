<?php

namespace Widget\request;

class widgetrequest {

    public $post = []; 

    function __construct(){
        /* 
        
        $data = file_get_contents('php://input');
        if ($data){
            $data = json_decode($data);
        } 
        
        */

        $this->post = ['hello' => 'world'];
    }
}