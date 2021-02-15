<?php

namespace App\Feeds\Rss090;

use App\Feeds\FakeFeed;
use App\Feeds\Variants;
use App\Utilities\Arr;

class Fake extends FakeFeed
{
    /**
     * What type of feed variant does this class represent?
     *
     * @return string
     */
    public function type(): string
    {
        return Variants::RSS090;
    }

    /**
     * Create a string representation of this fake feed.
     *
     * @return string
     */
    public function __toString(): string
    {
        $feed = <<<FEED
        <?xml version="1.0"?>
        <rdf:RDF
        xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
        xmlns="http://my.netscape.com/rdf/simple/0.9/">

            <channel>
                <title>{$this->simulator->title}</title>
                <link>{$this->simulator->linkToSource}</link>
                <description>{$this->simulator->subtitle}</description>
            </channel>

        FEED;

        foreach ($this->simulator->entries as $entry) {
            $feed .= $this->entryToString($entry);
        }

        $feed .= <<<FEED

        </rdf:RDF>
        FEED;

        return $feed;
    }

    /**
     * Create a string representation of an entry in this fake feed.
     *
     * @param array $entry
     * @return string
     */
    protected function entryToString(array $entry): string
    {
        $title = Arr::get($entry, 'title');
        $linkToSource = Arr::get($entry, 'linkToSource');

        return <<<ENTRY
        <item>
            <title>{$title}</title>
            <link>{$linkToSource}</link>
        </item>
        ENTRY;
    }
}
