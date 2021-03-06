<?php

namespace App\Utilities;

use App\Feeds\Link;
use App\Utilities\Arr;
use DOMDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class Xml
{
    /**
     * Retrieve the value of an attribute on a SimpleXMLElement
     *
     * @param SimpleXMLElement $xml
     * @param string $key
     * @param string|null $ns
     * @return mixed|null
     */
    public static function attributes($xml, $key = '', $ns = null)
    {
        // Attempt to cast the @attributes value to an array
        $object = $xml->attributes($ns, true);
        $arr = (array)$object;

        $attributes = [];
        if (array_key_exists('@attributes', $arr)) {
            // If there is an '@attributes' key available we are good to go
            $attributes = $arr['@attributes'];
        } else {
            // Otherwise fetch the attributes directly from this xml element
            foreach (iterator_to_array($object) as $key => $value) {
                $attributes[$key] = self::decode($value);
            }
        }

        // If have been given a key attempt to return that attribute value
        if (!empty($key)) {
            return Arr::get($attributes, $key, null);
        }

        // Otherwise return the entire attributes array.
        return $attributes;
    }

    /**
     * If a SimpleXMLElement is empty we can assume that it either didn't exist
     * in the first place or that there is nothing of interest to us anyway.
     *
     * @param SimpleXMLElement $xml
     * @return bool
     */
    public static function exists($xml)
    {
        return ! empty((string)$xml);
    }

    /*
     * Retrieve the content of an XML node as a string, retaining tags.
     *
     * @param SimpleXMLElement $xml
     * @param string $namespace
     * @return string
     */
    public static function content($xml): string
    {
        // Make sure we have content to work with.
        if (empty($xml)) {
            return '';
        }

        $xmlString = (string)$xml;

        // If the xml node has children nodes, we are working with a content node
        // tree, which needs an extra step for accurate string conversion.
        if (self::exists($xml->children())) {
            $xmlString = $xml->children()->saveXml();
        }

        // Parse the content as a DOM document.
        // https://www.php.net/manual/en/domdocument.savehtml.php#121444
        $doc = new DOMDocument();
        $doc->loadHtml('<html>' . $xmlString . '</html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Remove script tags
        $scriptTags = $doc->getElementsByTagName('script');
        foreach ($scriptTags as $tag) {
            $tag->parentNode->removeChild($tag);
        }

        $html = $doc->saveHTML();

        if (!$html) {
            return '';
        }

        return str_replace(array('<html>','</html>', "\n", "\t") , '' , $html);
    }

    /**
     * Decode the content of a an XML element by converting special characters
     * into regular characters and then returning the content as a string.
     *
     * @param SimpleXMLElement $xml
     * @return string
     */
    public static function decode($xml): string
    {
        return trim(htmlspecialchars_decode(self::string($xml), ENT_QUOTES));
    }

    /**
     * Convert an XML element to a string without altering the content.
     *
     * @param SimpleXMLElement $xml
     * @return string
     */
    public static function string($xml): string
    {
        return strval($xml);
    }

    /**
     * Extract link attributes from an XML element and represent them as
     * a collection of Link classes.
     *
     * @param SimpleXMLElement $xml
     * @param string $key
     * @return Collection
     */
    public static function links($xml, $key = 'link'): Collection
    {
        $links = collect();

        foreach ($xml->{$key} as $element) {
            // Retrieve the attributes from this element.
            $attributes = self::attributes($element);

            // If the attributes array is empty the link may be an XML value.
            if (! Arr::has($attributes, 'href') && $href = self::decode($xml->{$key})) {
                $attributes['href'] = $href;
            }

            // If the attributes array is still empty there is nothing to do.
            if (! empty($attributes)) {
                $link = Link::fromArray($attributes);
                $links->push($link);
            }
        }

        return $links;
    }

    /**
     * Attempt to parse an XML timestamp value and convert it to a Carbon instance.
     *
     * @param SimpleXMLElement $xml
     * @param string $key
     * @return Carbon|null
     */
    public static function timestamp($xml)
    {
        try {
            $value = self::decode($xml);
            if (!empty($value)) {
                return Carbon::parse($value);
            }
        } catch (\Throwable $th) {
            // we will ignore any parsing problems and return null instead.
        }

        return null;
    }
}
