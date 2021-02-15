<?php

namespace App\Feeds\Atom100;

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
        return Variants::ATOM100;
    }

    /**
     * Create a string representation of this fake feed.
     *
     * @return string
     */
    public function __toString(): string
    {

        $feed = <<<FEED
        <?xml version="1.0" encoding="utf-8"?>
        <feed xmlns="http://www.w3.org/2005/Atom">

            <id>{$this->simulator->identifier}</id>
            <link href="{$this->simulator->linkToSource}" rel="alternate" />
            <link href="{$this->simulator->linkToFeed}" rel="self" />
            <rights>{$this->simulator->rights}</rights>
            <subtitle>{$this->simulator->subtitle}</subtitle>
            <title>{$this->simulator->title}</title>
            <updated>{$this->simulator->timestamp->toAtomString()}</updated>
        FEED;

        // Authors
        foreach ($this->simulator->authors as $author) {
            $feed .= "\n<author>";
            if ($name = $author->getName()) {
                $feed .= "<name>{$name}</name>";
            }
            if ($email = $author->getEmail()) {
                $feed .= "<email>{$email}</email>";
            }
            if ($uri = $author->getUri()) {
                $feed .= "<uri>{$uri}</uri>";
            }
            $feed .= "</author>";
        }

        // Entries
        foreach ($this->simulator->entries as $entry) {
            $feed .= $this->entryToString($entry);
        }

        $feed .= <<<FEED

        </feed>
        FEED;

        return $feed;
    }

    /**
     * Create a string representation of an entry in this fake feed.
     *
     * @param array $entry
     * @return string
     */
    protected function entryToString(array $entry): string {
        $authors = Arr::get($entry, 'authors', []);
        $content = Arr::get($entry, 'content');
        $identifier = Arr::get($entry, 'identifier');
        $linkToSource = Arr::get($entry, 'linkToSource');
        $summary = Arr::get($entry, 'summary');
        $title = Arr::get($entry, 'title');
        $timestamp = Arr::get($entry, 'timestamp', now());

        $entry = <<<ENTRY
        <entry>
            <title>{$title}</title>
            <link href="{$linkToSource}" />
            <id>{$identifier}</id>
            <updated>{$timestamp->toAtomString()}</updated>
            <summary>
                <![CDATA[{$summary}]]>
            </summary>
            <content>
                <![CDATA[{$content}]]>
            </content>
        ENTRY;

        // Authors
        foreach ($authors as $author) {
            $entry .= "\n<author>";
            if ($name = $author->getName()) {
                $entry .= "<name>{$name}</name>";
            }
            if ($email = $author->getEmail()) {
                $entry .= "<email>{$email}</email>";
            }
            if ($uri = $author->getUri()) {
                $entry .= "<uri>{$uri}</uri>";
            }
            $entry .= "</author>";
        }

        return $entry .= '</entry>';
    }
}
