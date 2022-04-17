<?php

namespace Widgets\widget\tools;

use Widgets\state\state;
use Widgets\state\widgetstate;

class request {
    private $props;
    private $extra = [];
    function __construct(...$props) {
        $this->props = $props;
    }

    function then($jsFunction){
        $this->extra['then'] = $jsFunction;
        return $this;
    }

    function get($prop) {
        return isset($this->props[$prop]) ? $this->props[$prop] : '';
    }

    function apply(...$props) {
        $request = [
            'element' => 'widget_request',
            'props' => [
                'function' => $this->get('function'),
                'props' => $props,
                'url' => $this->get('url'),
                'class' => $this->get('class'),
                'useState' => $this->get('useState'),
                'extra' => $this->extra,
                'view' => '',
                'returnType' => $this->get('returnType'),
            ]
        ];

        return widgetstate::group($request);
    }

    function toElement(){
        return $this->apply();
    }

}