<?php

namespace App\Feeds\Rss100;

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
        return Variants::RSS100;
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
            xmlns="http://purl.org/rss/1.0/"
        >

        <channel rdf:about="{$this->simulator->linkToSource}">
            <title>{$this->simulator->title}</title>
            <link>{$this->simulator->linkToSource}</link>
            <description>{$this->simulator->subtitle}</description>

            <items>
            <rdf:Seq>
        FEED;

        foreach ($this->simulator->entries as $entry) {
            $linkToSource = Arr::get($entry, 'linkToSource');
            $feed .= <<<ENTRY
                    <rdf:li rdf:resource="{$linkToSource}" />
            ENTRY;
        }

        $feed .= <<<FEED
            </rdf:Seq>
            </items>
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
        $summary = Arr::get($entry, 'summary');

        return <<<ENTRY
        <item rdf:about="{$linkToSource}">
            <title>{$title}</title>
            <description>{$summary}</description>
            <link>{$linkToSource}</link>
        </item>
        ENTRY;
    }
}
