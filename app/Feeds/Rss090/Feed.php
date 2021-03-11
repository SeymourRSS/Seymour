<?php

namespace App\Feeds\Rss090;

use App\Feeds\Feed as ParentFeed;
use App\Feeds\Rss090\Entry;
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
        $this->subtitle = Xml::decode($this->xml->channel[0]->description);
        $this->title = Xml::decode($this->xml->channel[0]->title);

        // Entries
        $this->entries = collect();
        $namespaces = $this->getXmlNamespaces();
        foreach ($this->xml->item as $item) {
            $entry = new Entry();
            $entry->initialize($item, $namespaces);
            $this->entries->push($entry);
        }

        // Image
        if (Xml::exists($this->xml->image)) {
            $image = [];
            $image['height'] = Xml::decode($this->xml->image->height);
            $image['link'] = Xml::decode($this->xml->image->link);
            $image['title'] = Xml::decode($this->xml->image->title);
            $image['url'] = Xml::decode($this->xml->image->url);
            $image['width'] = Xml::decode($this->xml->image->width);
            $this->setExtra('image', array_filter($image));
        }

        // Links
        $this->linkToSource = Xml::links($this->xml->channel[0])
            ->whereIn('rel', ['none', 'alternate'])
            ->first();


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
        return Variants::RSS090;
    }
}
