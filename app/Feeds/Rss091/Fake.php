<?php

namespace App\Feeds\Rss091;

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
        return Variants::RSS091;
    }

    /**
     * Create a string representation of this fake feed.
     *
     * @return string
     */
    public function __toString(): string
    {
        $feed = <<<FEED
        <?xml version="1.0" encoding="ISO-8859-1" ?>
        <rss version="0.91">

        <channel>
            <title>{$this->simulator->title}</title>
            <link>{$this->simulator->linkToSource}</link>
            <description>{$this->simulator->subtitle}</description>
            <copyright>{$this->simulator->rights}</copyright>
            <pubDate>{$this->simulator->timestamp->toRssString()}</pubDate>
        FEED;

        // Image
        if ($url = $this->simulator->imageUrl) {
            $feed .= <<<IMAGE
            <image>
                <url>{$url}</url>
                <title>image</title>
                <link>{$this->simulator->linkToSource}</link>
                <width>32</width>
                <height>32</height>
            </image>
            IMAGE;
        }

        // Entries
        foreach ($this->simulator->entries as $entry) {
            $feed .= $this->entryToString($entry);
        }

        $feed .= <<<FEED
        </channel>
        </rss>
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

        $entry = <<<ENTRY
        <item>
            <title>{$title}</title>
            <link>{$linkToSource}</link>
        ENTRY;

        if ($summary) {
            $entry .= "<description>{$summary}</description>";
        }

        $entry .= '</item>';

        return $entry;
    }
}
