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

            $data = $value->getdata();
/*            
            $default = json_encode($value->getdefaults());
            $alias = json_encode($value->getalias());
*/

            $extra = [
                'default' => $value->getdefaults(),
                'alias' => $value->getalias(),
            ];

            $result .= "widgetstate.use(\"$key\", ". json_encode($data) .", ". json_encode($extra) .") \n\t\t\t";
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
        $stateClass = is_array($source)?$source[0]:$source;
        return is_array($source)?$stateClass::name($source[1]):$source::main();
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