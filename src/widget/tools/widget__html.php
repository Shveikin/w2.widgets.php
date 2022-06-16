<?php

namespace Widgets\widget\tools;

use Widgets\conventor\widgetconventor;

trait widget__html {

    public function toHTML(){
        $html = widgetconventor::$opentag;

        $html .= $this->element;
        $html .= $this->renderProps();
        $html .= widgetconventor::$closetag;
        $childs = $this->renderChilds();
        $html .= $childs;
        $html .= $this->closeTag();

        return $html;
    }
    
    private function renderProps() {
        $result = '';
        foreach($this->props as $attr => $value) {
            if ($attr=='className') $attr = 'class';
            
            if (!in_array($attr, ['innerHTML', 'innerText'])){
                $type = widgetconventor::getType($value);

                if (in_array($type, ['String', 'Int', 'Bool']))
                    $result .= " $attr='" . trim($value) . "'";
            }
        }
        
        return $result;
    }

    private function renderChilds(){
        $result = '';

        if ($this->innerHTML)
            $result = $this->innerHTML;
        else if (isset($this->props['innerText']))
            $result = $this->props['innerText'];
        else
            $result .= widgetconventor::toHTML($this->child);

        return $result;
    }

    private function closeTag(){
        if (!isset(widgetconventor::$singletags[$this->element])){
            return widgetconventor::$opentag . '/' .
                    $this->element .
                    widgetconventor::$closetag;
        } else {
            return '';
        }
    }

}