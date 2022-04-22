<?php

namespace Widgets\app;

use DI2\Container;
use Widgets\conventor\widgetconventor;
use Widgets\request\requeststorage;
use Widgets\state\widgetstate;

class widgetapp {
    use Container;

    public $htmlRender = true;
    public $script = true;
    public $title = 'widget app';
    public $structure = false;
    public $lang = 'en';
    public $cigaretteBurn = true;

    const SCRIPT_CONNECTED_MANUALLY = 32;
    const SCRIPT_CONNECTED_AUTOMATICALLY = 33;

    function __construct() {
        if ($this->script == self::SCRIPT_CONNECTED_AUTOMATICALLY) {
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
        if ($this->htmlRender) {
            $html = $widget->toHTML();

            if ($this->cigaretteBurn) {
                $html = ".$html.";
            }

        }

        $el = json_encode(widgetconventor::toElement($widget));
        $state = '';

        $script = '';
        $requeststoragescript = '';

        $appcount = $this->getappcount();
        if ($appcount == 1) {
            if ($this->script != false) {
                if ($this->script == self::SCRIPT_CONNECTED_AUTOMATICALLY) {
                    $script = "<script>$this->script_js_content</script>\n";
                } else
                if ($this->script != self::SCRIPT_CONNECTED_MANUALLY) {
                    $script = "<script src='$this->script'></script>\n";
                }
            }

            $state = widgetstate::render();

            $requeststorage = requeststorage::toElement();
            if (!empty($requeststorage)) {
                $requeststoragescript = 'requeststorage.create(' . json_encode($requeststorage) . ')';
            }
        }

        $selector = "app" . md5(rand()) . "{$appcount}";
        $result = <<<HTML

                    <div id='$selector'>$html</div>
                    <script>
                        $state
                        $requeststoragescript
                        c.render('#$selector',
                            $el
                        );
                    </script>
            HTML;

        if ($this->structure) {
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