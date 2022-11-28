<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use SimpleXMLElement;

class XmlToArray
{
    /**
     * @param string $xml
     * @param string $tag
     * @return array
     * @throws Exception
     */
    public function convert(string $xml, string $tag): array
    {
        return $this->toArray($xml, $tag);
    }

    /**
     * @param $xml
     * @param $tag
     * @return array
     * @throws Exception
     */
    public function toArray($xml, $tag): array
    {
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);
        $response = new SimpleXMLElement($response, LIBXML_NOCDATA);
        $body = $response->soapBody->{$tag};
        return json_decode(json_encode((array)$body), TRUE);
    }
}
