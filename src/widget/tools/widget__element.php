<?php


namespace Widgets\widget\tools;

use Widgets\conventor\widgetconventor;

trait widget__element {
    public function toElement($json = false){
        $props = [
            'json' => $json,
        ];

        foreach($this->props as $prop => $val){
            $value = widgetconventor::toElement($val);
            $trim = widgetconventor::getTrimFor($prop);
            if ($trim && is_string($value))
                $value = trim($value, $trim);
                
            $props[$prop] = $value;
        }

        foreach($this->child as $child){
            if (!isset($props['child'])) $props['child'] = [];

            array_push($props['child'], $child);// widgetconventor::toElement($child));
        }

        return [
            'element' => $this->element,
            'props' => $props,
        ];
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