<?php

namespace Widgets\app;

use Widgets\conventor\widgetconventor;
use Widgets\request\requeststorage;
use Widgets\state\widgetstate;

abstract class widgetapp__tools extends widgetapp__render {

    private static $appcount = 0;

    function __construct() {
        if (static::script == widgetapp::SCRIPT_CONNECTED_AUTOMATICALLY) {
            $this->script_js_content = str_replace("\n", ' ', file_get_contents(__DIR__ . '/js/cs.min.js'));
        }
    }


    protected function getappcount() {
        return ++self::$appcount;
    }





    protected function app($element) {
        $widget = widgetconventor::toWidget($element);

        $appcount = $this->getappcount();
        $selector = "app" . md5(rand()) . "{$appcount}";

        $html = $this->render_html($widget);
        $app  = $this->render_template($widget, $selector);



        $script = $this->get_script($appcount);
        $state  = $this->render_state();
        $requeststoragescript = $this->render_requests($appcount);



        $result = <<<HTML
            <div id='$selector'>$html</div>
            $state
            $app
            $requeststoragescript
        HTML;

        if (static::structure) {
            $result = <<<HTML
                            <!DOCTYPE html>
                            <html lang="{$this->lang}">
                            <head>
                                <meta charset="UTF-8">
                                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>{$this->title}</title>
                                {$script}
                            </head>
                                <body>{$result}
                                </body>
                            </html>
                        HTML;
        } else {
            $result = $script . $result;
        }

        return $result;
    }



    /** 
     * переименовать на target
    */
    static function route() {
        requeststorage::main();
    }



    /** 
     * вывод всего что осталось
    */
    static function completion(){
        $requeststorage = requeststorage::toElement();
        $requeststoragescript = '';
        if (!empty($requeststorage)) {
            $requeststoragescript = 'requeststorage.create(' . json_encode($requeststorage) . ')';
        }

        $state = widgetstate::render();
        $result = <<<HTML
                <script>
                    $state
                    $requeststoragescript
                </script>
        HTML;

        return $result;
    }

}