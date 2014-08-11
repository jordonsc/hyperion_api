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
    protected function jsonToList($txt)
    {
        if ($txt) {
            $struct = @json_decode($txt);
            if (!is_array($struct)) {
                return $txt;
            }
            $txt = implode("\n", $struct);
        }
        return $txt;
    }

    /**
     * Covert a new-line delimited list into a JSON array
     *
     * @param string $txt
     * @return string
     */
    protected function listToJson($txt)
    {
        $txt = str_replace("\r\n", "\n", $txt);
        return json_encode(explode("\n", $txt));
    }

    /**
     * Covert a JSON array into a new-line delimited associative list
     *
     * @param string $txt
     * @return string
     */
    protected function jsonToListAssoc($txt)
    {
        if ($txt) {
            $struct = @json_decode($txt, true);
            if (!is_array($struct)) {
                return $txt;
            }
            $txt = '';
            foreach ($struct as $key => $value) {
                $txt .= $key.' = '.$value."\n";
            }
        }
        return $txt;
    }

    /**
     * Covert a new-line delimited associative list into a JSON array
     *
     * @param string $txt
     * @return string
     */
    protected function listToJsonAssoc($txt)
    {
        $data  = [];
        $txt   = str_replace("\r\n", "\n", $txt);
        $lines = explode("\n", $txt);
        foreach ($lines as $line) {
            $parts = explode('=', $line, 2);
            $key   = trim($parts[0]);
            if ($key) {
                $value = count($parts) > 1 ? trim($parts[1]) : $key;
                $data[$key] = $value;
            }
        }
        return json_encode($data);
    }

}