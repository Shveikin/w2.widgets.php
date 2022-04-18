<?php


namespace Widgets\state;

use DI2\Container;
use DI2\MP;
use Exception;
use Widgets\conventor\widgetconventor;

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
            $data = json_encode($value->getdata());
            $default = json_encode($value->getdefaults());
            $alias = json_encode($value->getalias());

            $result .= "widgetstate.use(\"$key\", $data, $default, $alias) \n\t\t\t";
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

    static function group(...$args){
        $list = [];
        foreach ($args as $itm) {
            if (widgetconventor::canConvertToElement($itm)){
                $list[] = widgetconventor::toElement($itm);
            }
        }

        $result = [
            'element' => 'group',
            'props' => [
                'list' => $list
            ],
        ];
        return $result;

    }

}