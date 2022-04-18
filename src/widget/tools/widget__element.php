<?php


namespace Widgets\widget\tools;

use Widgets\conventor\widgetconventor;

trait widget__element {
    public function toElement(){
        $result = [
            'element' => $this->element
        ];

        foreach($this->props as $prop => $val){
            if (!isset($result['props'])) $result['props'] = [];

            $value = widgetconventor::toElement($val);
            $trim = widgetconventor::getTrimFor($prop);
            if ($trim && is_string($value))
                $value = trim($value, $trim);
            $result['props'][$prop] = $value;
        }

        foreach($this->child as $child){
            if (!isset($result['props'])) $result['props'] = [];
            if (!isset($result['props']['child'])) $result['props']['child'] = [];
            
            array_push($result['props']['child'], widgetconventor::toElement($child));
        }

        return $result;
    }

    public function getUseStateDataWithSource(){
        $result = [];
        foreach ($this->useState as $source) {
            $stateClass = is_array($source)?$source[0]:$source;
            $state = is_array($source)?$stateClass::name($source[1]):$source::main();
            $shortName = $state->getName();

            if (!isset($result[$shortName])) $result[$shortName] = [];
            $result[$shortName]['data'] = $state->getdata();
            $result[$shortName]['source'] = $source;
        }

        return $result;
    }
}