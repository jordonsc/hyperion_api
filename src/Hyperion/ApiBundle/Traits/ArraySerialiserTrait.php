<?php
namespace Hyperion\ApiBundle\Traits;

trait ArraySerialiserTrait
{

    /**
     * Covert a JSON array into a new-line delimited list
     *
     * @param string $txt
     * @return string
     */
    protected function jsonToList($txt) {
        if ($txt) {
            $txt = implode("\n", json_decode($txt));
        }
        return $txt;
    }

    /**
     * Covert a new-line delimited list into a JSON array
     *
     * @param string $txt
     * @return string
     */
    protected function listToJson($txt) {
        $txt = str_replace("\r\n", "\n", $txt);
        return json_encode(explode("\n", $txt));
    }

}