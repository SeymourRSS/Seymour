<?php

namespace App\Feeds;

use App\Utilities\Arr;
use Illuminate\Support\Collection;

class Modules
{
    /**
     * The list of namespaces that we currently know how to parse.
     */
    public const LIST = [
        'http://www.itunes.com/dtds/podcast-1.0.dtd' => 'ItunesV1',
        'http://purl.org/rss/1.0/modules/content/' => 'ContentV1'
    ];

    /**
     * Given a list of namespaces from an XML document, determine which of
     * them are known to us and can be parsed.
     *
     * @param array $namespaces
     * @return Collection
     */
    public static function available(array $namespaces)
    {
        return collect($namespaces)
            ->filter(function ($uri, $key) {
                return Arr::has(self::LIST, $uri);
            })
            ->map(function ($uri) {
                return Arr::get(self::LIST, $uri);
            }, collect());
    }
}
