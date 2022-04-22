<?php

namespace Widgets\request;

use DI2\Container;
use DI2\MP;
use Widgets\state\widgetstate;

class requeststorage {
    use Container;

    private $post = []; 
    private $get = []; 
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

            $useStatesWithSource = [];
            foreach ($useState as $stateSource) {
                if (is_array($stateSource)){
                    $useStatesWithSource[] = $stateSource;
                } else {
                    $useStatesWithSource[] = [$stateSource, $stateSource::getName()];
                }
            }

            $this->storage[$requestHash] = [
                'source' => $source,
                'method' => $method,
                'url' => $url,
                'useState' => $useStatesWithSource,
            ];
        }

        return new widgetrequest($requestHash);
    }


    protected function toElement(){
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



    protected function fill($state){
        $result = [];

        foreach ($state->getAliasList() as $url => $statekey) {
            if (isset($this->get[$url])){
                $result[$statekey] = $this->get[$url];
            }
        }

        return $result;
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