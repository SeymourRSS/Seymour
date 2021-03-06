<?php

namespace App\Feeds\Rss200;

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
        return Variants::RSS200;
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
        <rss version="2.0">

        <channel>
            <title>{$this->simulator->title}</title>
            <link>{$this->simulator->linkToSource}</link>
            <description>{$this->simulator->subtitle}</description>
            <pubDate>{$this->simulator->timestamp->toRssString()}</pubDate>
            <copyright>{$this->simulator->rights}</copyright>
        FEED;

        foreach ($this->simulator->categories as $category) {
            $feed .= "<category>{$category}</category>\n";
        }

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
        $authors = Arr::get($entry, 'authors', []);
        $categories = Arr::get($entry, 'categories', []);
        $identifier = Arr::get($entry, 'identifier');
        $linkToSource = Arr::get($entry, 'linkToSource');
        $summary = Arr::get($entry, 'summary');
        $timestamp = Arr::get($entry, 'timestamp', now());
        $title = Arr::get($entry, 'title');

        $entry = <<<ENTRY
        <item>
            <title>{$title}</title>
            <pubDate>{$timestamp->toRssString()}</pubDate>
            <description>{$summary}</description>
            <link>{$linkToSource}</link>
            <guid>{$identifier}</guid>
        ENTRY;

        foreach($categories as $category) {
            $entry .= "<category>{$category}</category>";
        }

        foreach ($authors as $author) {
            $entry .= "<author>{$author->getEmail()}</author>\n";
        }

        return $entry .= '</item>';
    }
}
