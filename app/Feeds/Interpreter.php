<?php

namespace App\Feeds;

use App\Feeds\Exceptions\InvalidXmlException;
use App\Feeds\Exceptions\UnknownFeedVariantException;
use App\Feeds\Variants;
use App\Utilities\Arr;
use App\Utilities\Str;
use SimpleXMLElement;

class Interpreter
{
    /**
     * Parse xml content from a string and classify it as an RSS variant.
     *
     * @param string $content
     * @throws InvalidFeedException
     * @throws UnknownFeedVariantException
     * @return Feed
     */
    public static function parse(string $content)
    {
        // Parse the XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOCDATA);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new InvalidXmlException($content, $errors);
        }

        // Classify the feed variant
        $variant = self::classify($xml);

        if (empty($variant)) {
            throw new UnknownFeedVariantException($content);
        }

        // Create a new Feed instance
        $feed = Variants::make($variant);
        $feed->initialize($xml);

        return $feed;
    }

    /**
     * Attempt to determine the feed variant.
     *
     * @param SimpleXMLElement $xml
     * @return string|null
     */
    public static function classify(SimpleXMLElement $xml)
    {
        $root = $xml->getName();
        $attributes = $xml->attributes();
        $namespaces = $xml->getDocNamespaces();

        // dd($root, $attributes, $namespaces);

        // Check for RSS 0.90
        if ($root == 'RDF' && Str::contains(Arr::get($namespaces, ''), 'rdf/simple/0.9')) {
            return Variants::RSS090;
        }

        // Check for RSS 0.91
        if ($root == 'rss' && (string)$attributes->version == '0.91') {
            return Variants::RSS091;
        }

        // Check for RSS 0.92
        if (Str::contains($xml, 'rss version="0.92"')) {
            return Variants::RSS091;
        }

        // Check for RSS 1.00
        if ($root == 'RDF' && Str::contains(Arr::get($namespaces, ''), 'purl.org/rss/1.')) {
            return Variants::RSS100;
        }

        // Check for RSS 2.00
        if ($root == 'rss' && Str::startsWith((string)$attributes->version, '2')) {
            return Variants::RSS200;
        }

        // Check for Atom 1.00
        if ($root == 'feed' && Str::contains(Arr::get($namespaces, ''), '2005/Atom')) {
            return Variants::ATOM100;
        }

        // Otherwise this variant is unknown.
        return null;
    }
}
