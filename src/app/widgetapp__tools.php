<?php

namespace Widgets\app;

use Widgets\conventor\widgetconventor;
use Widgets\hash\__h32;
use Widgets\request\requeststorage;
use Widgets\state\widgetstate;

abstract class widgetapp__tools {

    const htmlRender = true;
    const structure = false;
    const cigaretteBurn = true;
    const hashApp = true;
    const script = true;



    function __construct() {
        if (static::script == widgetapp::SCRIPT_CONNECTED_AUTOMATICALLY) {
            $this->script_js_content = str_replace("\n", ' ', file_get_contents(__DIR__ . '/js/w2.mini.js'));
        }
    }

    private static $appcount = 0;

    protected function getappcount() {
        return ++self::$appcount;
    }

    protected function app($element) {
        $widget = widgetconventor::toWidget($element);

        $html = '';
        if (static::htmlRender) {
            $html = $widget->toHTML();

            if (static::cigaretteBurn) {
                $html = ".$html.";
            }
        }


        $app = '';

        if (static::hashApp){
            /* 
            $el = json_encode(widgetconventor::hashElement($widget));
            $hashList = json_encode(widgetconventor::getHashList());

            $app = "hashc(
                $hashList, 
                $el
            )"; 
            */
            $app = json_encode(new __h32($widget));
        } else {
            $app = json_encode($widget);
        }

        
        

        $script = '';
        $requeststoragescript = '';

        $appcount = $this->getappcount();
        if ($appcount == 1) {
            if (static::script != false) {
                if (static::script == widgetapp::SCRIPT_CONNECTED_AUTOMATICALLY) {
                    $script = "<script>$this->script_js_content</script>\n";
                } else
                if (static::script != widgetapp::SCRIPT_CONNECTED_MANUALLY) {
                    $script = "<script src='$this->script'></script>\n";
                }
            }

            

            $requeststorage = requeststorage::toElement();
            if (!empty($requeststorage)) {
                $requeststoragescript = 'requeststorage.create(' . json_encode($requeststorage) . ')';
            }
        }




        $selector = "app" . md5(rand()) . "{$appcount}";

        $state = widgetstate::render();
        $result = <<<HTML

                    <div id='$selector'>$html</div>
                    <script>
                        $state
                        $requeststoragescript
                        c.render('#$selector',
                            $app
                        );
                    </script>
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

    static function route() {
        requeststorage::main();
    }

}