<?php

namespace Widgets\request;

use DI2\Container;
use DI2\MP;
use Widgets\state\widgetstate;

class requeststorage {
    use Container;

    public $post = []; 
    public $get = false; 
    private $storage = [];
    private $alias = [];


    function __construct($super){
        $super($this);

        $data = file_get_contents('php://input');

        if ($data){
            $data = json_decode($data, true);
            if ($data){
                if ($data['state']){
                    $this->applyPostDataToStates($data['state']);
                }

                if ($data['executor'])
                    $this->execute($data['executor'], $data['request_id']);
            }
        } else {
            $this->get = $_GET;
        }

    }


    protected function getHash($source, $method, $url){
        return md5($source . $method . $url);
    }

    protected function get($source, $method, $url, $useState){
        $hash = $this->getHash($source, $method, $url);
        $requestHash = isset($this->alias[$hash])?$this->alias[$hash]:$method;



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


    protected function setAlias($alias, $hash){
        $this->alias[$hash] = $alias;
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


    protected function execute($executor, $resuest_id){
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
            'current_request' => $resuest_id
        ];

        die(json_encode($result));
    }



    protected function applyPostDataToStates($states){
        foreach ($states as $option) {
            if (isset($option['source'])){
                $src = implode('::', $option['source']); 
                $this->post[$src] = $option['data'];


                // $state = widgetstate::source($option['source']);
                // $state->setdata($option['data'], 'request');
            }
        }
    }

    protected function getBySource($source){
        $src = implode('::', $source);
        if (isset($this->post[$src])){
            return $this->post[$src];
        } else {
            return false;
        }
    }
}