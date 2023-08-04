<?php

namespace Devpark\PayboxGateway\Utils;

use SimpleXMLElement;

class XmlUtils
{
    private static function arrayToXml($data, &$xmlData)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $subnode = $xmlData->addChild($key);
                self::arrayToXml($value, $subnode);
            } else {
                $xmlData->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    public static function arrayToXmlString($data, $root = 'data')
    {
        $xmlData = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><$root></$root>");
        self::arrayToXml($data, $xmlData);
        return str_replace("\n", '', $xmlData->asXml());
    }
}
