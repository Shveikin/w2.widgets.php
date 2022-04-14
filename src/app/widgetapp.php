<?php

namespace Widgets\app;

use DI2\Container;
use Widgets\conventor\widgetconventor;

class widgetapp {
    use Container;
    
    public $script = false;
    public $title = 'widget app';
    public $structure = false;
    public $lang = 'en';

    function __construct(){
        $this->script = file_get_contents(__DIR__ . '/js/w2.mini.js');
    }

    private static $appcount = 0; 

    protected function getappcount(){
        return ++self::$appcount;
    }


    protected function c($element){
        $widget = widgetconventor::toWidget($element);
        $html = $widget->toHTML();
        $el = json_encode(widgetconventor::toElement($widget));

        $result = '';

        $appcount = $this->getappcount();
        if ($this->script && $appcount==1){
            if ($this->script==true){
                $result .= "<script>$this->script</script>\n";
            } else {
                $result .= "<script src='$this->script'></script>\n";
            }
        }



        $selector = "app".rand(32, 9992)."{$appcount}";
        $result .= <<<HTML
            <div id='$selector'>.$html.</div>
            <script>
                c.render('#$selector',$el);
            </script>
            HTML;


        if ($this->structure)
        $result = <<<HTML
            <!DOCTYPE html>
            <html lang="{$this->lang}">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$this->title}</title>
            </head>
                <body>
                    {$result}
                </body>
            </html>
        HTML;

        return $result;
    }
}