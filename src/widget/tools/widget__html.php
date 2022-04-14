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
            if (!in_array($attr, ['innerHTML', 'innerText']))
                $result .= " $attr='" . trim($value) . "'";
        }
        
        return $result;
    }

    private function renderChilds(){
        $result = '';

        if (isset($this->props['innerHTML']))
            $result = $this->props['innerHTML'];
        else if (isset($this->props['innerText']))
            $result = $this->props['innerText'];
        else
            foreach($this->child as $child){
                $result .= widgetconventor::toHTML($child);
            }

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