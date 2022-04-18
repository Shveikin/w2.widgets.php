<?php

namespace Widgets\widget\tools;

use DI2\Container;
use DI2\MP;
use Widgets\state\state;
use Widgets\state\widgetstate;

class widgetrequest {
    use Container;

    private $post = []; 
    private $get = []; 

    function __construct($super){
        $super($this);
        $data = file_get_contents('php://input');
        if ($data){
            $data = json_decode($data);
            if ($data){

                if ($data->state)
                    $this->applyPostDataToStates($data->state);

                
                if ($data->executor)
                    $this->execute($data->executor);

            }
        }

        $this->get = $_GET;
    }

    private function applyPostDataToStates($states){
        foreach ($states as $state) {
            if (is_array($state->source)){
                $className = $state->source[0];
                $stateName = $state->source[1];

                $className::name($stateName)->setFromRequest((array) $state->data);
            }
        }
    }

    private function execute($executor){
        $ob_length = ob_get_length();
        ob_get_clean();

        $class = $executor->source;
        $method = $executor->method;
        $function_props = (array) $executor->props;

        $instance = MP::GET($class);
        if ($executor->bind)
            $instance->bind = (array) $executor->bind;
        $functionResult = $instance->{$method}(...$function_props);


        $state = $instance->getUseStateDataWithSource();
        
        $result = [
            'result' => $functionResult,
            'state' => $state,
            'rem' => $ob_length,
        ];

        die(json_encode($result));
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