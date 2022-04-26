<?php


namespace Widgets\state;

use JsonSerializable;
use Widgets\request\requeststorage;

class widgetstate__tools implements stateinterface, JsonSerializable {

    public function jsonSerialize(){
        return [];
    }

    protected function readget(){
        $requeststorage = requeststorage::main();


        $result = [];

        foreach ($this->getAliasList() as $statekey => $url) {
            if (isset($requeststorage->get[$url])){

                $val = $requeststorage->get[$url];
                $isArray = str_starts_with($statekey, '_');
                if ($isArray)
                    $val = explode(',', $val);
                else
                    $val = [$val];


                switch (static::data_type) {
                    case static::DATA_INT:
                        $val = array_map(function($itm){
                            return (int)$itm;
                        }, $val);
                    break;
                    case static::DATA_FLOAT:
                        $val = array_map(function($itm){
                            return (float)$itm;
                        }, $val);
                    break;
                }


                $result[$statekey] = $isArray?$val:$val[0];
            }
        }



        if (!empty($result))
            $this->setdata($result, 'get load');
        
    }



    // interface default
    function default(): array {
        return [];
    }

    function alias(): array|bool {
        return false;
    }

    function revice($key, $value){
        
    }

    function onchange(){
        return false;
    }

}