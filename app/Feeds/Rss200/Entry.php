<?php

namespace App\Feeds\Rss200;

use App\Feeds\Author;
use App\Feeds\Entry as ParentEntry;
use App\Feeds\Variants;
use App\Utilities\Xml;

class Entry extends ParentEntry
{
    /**
     * Read the values in the entry's default XML namespace.
     *
     * @return void
     */
    public function readDefaultNamespace(): void
    {
        // Attributes
        $this->summary = Xml::decode($this->xml->description);
        $this->timestamp = Xml::timestamp($this->xml->pubDate);
        $this->title = Xml::decode($this->xml->title);

        // Authors
        $this->authors = collect();
        foreach ($this->xml->author as $person) {
            $this->authors->push(new Author(Xml::decode($person)));
        }

        // Categories
        $categories = collect();
        foreach ($this->xml->category as $category) {
            $categories->push(ucwords(Xml::decode($category)));
        }
        if ($categories->isNotEmpty()) {
            $this->setExtra('categories', $categories->toArray());
        }

        // Links
        $this->setExtra('links', Xml::links($this->xml));
        $this->linkToSource = $this->getExtra('links')
            ->whereIn('rel', ['none', 'alternate'])
            ->first();

        // Identifier
        $this->identifier = $this->generateIdentifier($this->xml->guid);
    }

    /**
     * Read the values from the "content" namespace, if present.
     *
     * @param string $key
     * @return void
     */
    public function readContentV1Namespace($key)
    {
        $children = $this->xml->children($key, true);

        if (Xml::exists($children->encoded)) {
            $this->content = Xml::content($children->encoded);
        }
    }

    /**
     * Read the values from the "content" namespace, if present.
     *
     * @param string $key
     * @return void
     */
    public function readItunesV1Namespace($key)
    {
        $itunes = [];

        foreach ($this->xml->children($key, true) as $key => $value) {
            if (Xml::exists($value)) {
                $itunes[$key] = Xml::decode($value);
            }
        }

        $this->setExtra('itunes', $itunes);
    }

    /**
     * What type of feed is this?
     *
     * @return string
     */
    public function getVariant(): string
    {
        return Variants::RSS200;
    }
}
