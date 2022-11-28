<?php

namespace App\Services;

class ArrayToXml
{
    public function convert(array $value, $rootTag): bool|string
    {
        $xml = new \SimpleXMLElement("<{$rootTag}/>");
        $xml->addAttribute('encoding', 'UTF-8');
        $this->toXml($xml, $value);

        header('Content-type: text/xml');

        return $xml->asXML();
    }

    public function toXml(\SimpleXMLElement $object, array $data, $level = 0)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                $this->toXml($new_object, $value, $level + 1);
            } else {
                $object->addChild($key, $value);
            }
        }

    }
}
