<?php


namespace Widgets\state;

use DI2\Container;
use DI2\MP;
use Exception;

class widgetstate {
    use Container;

    private $global = [];
    private $hash = [];
    private $post = [];


    function __construct($super){ 
        $super($this);
    }


    private function render(){
        $result = '';
        foreach ($this->global as $key => $value) {
            if (!$value->isRendered()) {
                $data = $value->export('data');
                $extra = [];
                foreach (['default', 'alias', 'onchange', 'delay'] as $stateProp) {
                    $stateData = $value->export($stateProp);
                    if (!empty($stateData) && $stateData!=false)
                        $extra[$stateProp] = $stateData;
                }
                if ((!empty($data) && $data!=false) || !empty($extra)){
                    $result .= "widgetstate.use(\"$key\", ". json_encode($data) .   (!empty($extra)?", ".json_encode($extra):'')   . ") \n\t\t\t";
                    // $this->rendered[$key] = true;
                    $value->setRendered();
                }
            }
        } 
        return $result;
    }

    private function toElement(){
        $result = [];
        foreach ($this->global as $key => $value) {
            $data = $value->getdata();
            $result[$key] = $data;
        } 
        return $result;
    }

    private function name($stateName, $source = false){
        if (!isset($this->global[$stateName])){
            $state = MP::GET(
                class: $source?$source:state::class, 
                alias: $stateName,
                constructor: $stateName
            );
            $this->global[$stateName] = $state;
        }

        return $this->global[$stateName];
    }


    private function reg(state $state, $customName = false){
        $stateName = '___';
        if ($customName==false){
            $stateName = (new \ReflectionClass($state))->getShortName();
            if ($stateName=='state') $stateName = '';

            if (isset($this->global[$stateName])){
                if (!isset($this->hash[$stateName])){
                    $this->hash[$stateName] = 1;
                }

                $stateName .= '_' . $this->hash[$stateName]++;
            }
        } else {
            $stateName = $customName;
        }
        $this->global[$stateName] = $state;
        $state->setName($stateName);
    }


    private function create(...$props){
        $name = $props['name'];

        $classFrom = state::class;
        if (isset($props['from'])) {
            if (!is_subclass_of($props['from'], state::class)){
                throw new Exception("$props[from] class должен наследоваться от state");
            }
            $classFrom = $props['from'];
        }

        $stateName = widgetstate::getChildName($classFrom, $name);

        $default = isset($props['default'])?$props['defalut']:false;

        $this->global[$stateName] = new $classFrom(default: $default);
        return $this->global[$stateName];
    }

    static function getChildName($classParrent, $suffix){
        $parentClassName = (new \ReflectionClass(static::class))->getShortName();
        return $parentClassName . $suffix;
    }

    private function global(){
        return $this->global;
    }


    static function source($source): state {
        $stateClass = false;
        $stateName = false;
        if (is_array($source)){
            if (str_starts_with(strrev($source[0]), strrev($source[1]))){
                $stateClass = $source[0];
            } else {
                $stateClass = $source[0];
                $stateName = $source[1];
            }
        }

        
        return $stateName?$stateClass::name($stateName):$stateClass::main();
    }


    /** 
     * Список изменнех стейтов
     * возвращается на фронт
    */
    private $changedStateList = [];
    private function changedState($source, $change = true){
        $result = [
            'changed' => $change,
            'source' => $source,
        ];
        $this->changedStateList[implode('|', (array)$source)] = $result;
    }

    private function getChangedStateDataWithSource(): array {
        $result = [];

        foreach ($this->changedStateList as $changed) {
            if ($changed['changed']){
                $source = $changed['source'];

                $state = widgetstate::source($source);
                $shortName = $state->getName();

                if (!isset($result[$shortName])) $result[$shortName] = [];
                $result[$shortName]['data'] = $state->getdata();
                $result[$shortName]['source'] = $source;
            }
        }

        return $result;
    }


}