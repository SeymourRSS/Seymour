<?php

namespace App\Feeds\Rss200;

use App\Feeds\Feed as ParentFeed;
use App\Feeds\Variants;
use App\Utilities\Xml;

class Feed extends ParentFeed
{
    /**
     * Read the values in the feed's default XML namespace.
     *
     * @return void
     */
    public function readDefaultNamespace(): void
    {
        // Attributes
        $this->rights = Xml::decode($this->xml->channel[0]->copyright);
        $this->subtitle = Xml::decode($this->xml->channel[0]->description);
        $this->title = Xml::decode($this->xml->channel[0]->title);

        // Categories
        $categories = collect();
        foreach ($this->xml->channel[0]->category as $category) {
            $categories->push(ucwords(Xml::decode($category)));
        }
        if ($categories->isNotEmpty()) {
            $this->setExtra('categories', $categories->toArray());
        }

        // Entries
        $this->entries = collect();
        $namespaces = $this->getXmlNamespaces();
        foreach ($this->xml->channel[0]->item as $item) {
            $entry = new Entry();
            $entry->initialize($item, $namespaces);
            $this->entries->push($entry);
        }

        // Image
        if (Xml::exists($this->xml->channel[0]->image)) {
            $image = [];
            $image['height'] = Xml::decode($this->xml->channel[0]->image->height);
            $image['link'] = Xml::decode($this->xml->channel[0]->image->link);
            $image['title'] = Xml::decode($this->xml->channel[0]->image->title);
            $image['url'] = Xml::decode($this->xml->channel[0]->image->url);
            $image['width'] = Xml::decode($this->xml->channel[0]->image->width);
            $this->setExtra('image', array_filter($image));
        }

        // Links
        $this->linkToSource = Xml::links($this->xml->channel[0])
            ->whereIn('rel', ['none', 'alternate'])
            ->first();

        // Timestamp
        $this->timestamp = Xml::timestamp($this->xml->channel[0]->pubDate);
        if (empty($this->timestamp)) {
            $this->timestamp = Xml::timestamp($this->xml->channel[0]->lastBuildDate);
        }

        // Identifier
        $this->identifier = $this->generateIdentifier();
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
