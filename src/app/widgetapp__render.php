<?php

namespace Widgets\app;

use Widgets\hash\__h32;
use Widgets\request\requeststorage;
use Widgets\state\widgetstate;

abstract class widgetapp__render {
    const htmlRender = true;
    const structure = false;
    const cigaretteBurn = true;
    const hashApp = true;
    const onlyComplection = false;
    const script = true;

    private function get_script($appcount){
        $result = '';

        if ($appcount == 1) {
            if (static::script != false) {
                if (static::script == widgetapp::SCRIPT_CONNECTED_AUTOMATICALLY) {
                    $result = "<script>$this->script_js_content</script>\n";
                } else
                if (static::script != widgetapp::SCRIPT_CONNECTED_MANUALLY) {
                    $result = "<script src='$this->script'></script>\n";
                }
            }
        }

        return $result;
    }

    private function render_html($widget){
        $result = '';

        if (static::htmlRender) {
            $html = $widget->toHTML();

            if (static::cigaretteBurn) {
                $html = ".$html.";
            }
        }

        return $result;
    }

    private function render_template($widget, $selector){
        $result = '';
        if (static::hashApp){
            $__hash__ = (new __h32($widget))->hash64();
            $result = json_encode($__hash__);
            $result = "<script type='cyberscript' target='$selector'>$result</script>";
        } else {
            $result = json_encode($widget);
            $result = "<script>
                c.render('#$selector',
                    $result
                );
            </script>";
        }

        return $result;
    }

    private function render_state(){
        return !static::onlyComplection
            ?widgetstate::render()
            :'';
    }


    private function render_requests($appcount){
        $result = '';
        if ($appcount == 1) {
            if (!static::onlyComplection){
                $requeststorage = requeststorage::toElement();
                if (!empty($requeststorage)) {
                    $result = 'requeststorage.create(' . json_encode($requeststorage) . ')';
                }
            }
        }

        return $result;
    }

}