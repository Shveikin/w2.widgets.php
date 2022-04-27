<?php

namespace Widgets\request;

use DI2\Container;
use DI2\MP;
use Widgets\state\widgetstate;

class requeststorage {
    use Container;

    public $post = []; 
    public $get = []; 
    private $storage = [];


    function __construct($super){
        $super($this);

        $this->get = $_GET;
        $data = file_get_contents('php://input');

        if ($data){
            $data = json_decode($data, true);
            if ($data){
                if ($data['state']){
                    $this->applyPostDataToStates($data['state']);
                }

                if ($data['executor'])
                    $this->execute($data['executor']);
            }
        }

    }


    protected function get($source, $method, $url, $useState){
        $requestHash = md5($source . $method . $url);

        if (!isset($this->storage[$requestHash])){

            $this->storage[$requestHash] = [
                'source' => $source,
                'method' => $method,
                'url' => $url,
                'useState' => $useState,
            ];
        }

        return new widgetrequest($requestHash);
    }


    protected function toElement(){
        foreach ($this->storage as $requestHash => $request) {
            $useStatesWithSource = [];
            $useState = $request['useState'];

            foreach ($useState as $stateSource) {
                if (is_array($stateSource)){
                    $useStatesWithSource[] = $stateSource;
                } else {
                    $stateNamespace = explode('\\', $stateSource);
                    $useStatesWithSource[] = [$stateSource, end($stateNamespace)];
                }
            }

            $this->storage[$requestHash]['useState'] = $useStatesWithSource;
        }

        return $this->storage;
    }


    protected function execute($executor){
        $ob_length = ob_get_length();
        ob_get_clean();

        $class = $executor['source'];
        $method = $executor['method'];
        $bind = isset($executor['bind'])?$executor['bind']:[];

        $instance = MP::GET($class);
        $functionResult = $instance->{$method}(...$bind);

        // state::getChangedDataWith
        // $state = $instance->getUseStateDataWithSource();
        $state = widgetstate::getChangedStateDataWithSource();

        
        $result = [
            'result' => $functionResult,
            'state' => $state,
            'rem' => $ob_length,
        ];

        die(json_encode($result));
    }



    protected function applyPostDataToStates($states){
        foreach ($states as $option) {
            if (isset($option['source'])){
                $state = widgetstate::source($option['source']);
                $state->setdata($option['data'], 'from request');
            }
        }
    }
}