<?php

namespace App\Feeds\Atom100;

use App\Feeds\Author;
use App\Feeds\Feed as ParentFeed;
use App\Feeds\Variants;
use App\Utilities\Xml;
use Illuminate\Support\Carbon;

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
        $this->rights = Xml::decode($this->xml->rights);
        $this->subtitle = Xml::decode($this->xml->subtitle);
        $this->timestamp = Xml::timestamp($this->xml->updated);
        $this->title = Xml::decode($this->xml->title);

        // Authors
        $this->authors = collect();
        foreach ($this->xml->author as $person) {
            $this->authors->push(new Author(
                Xml::decode($person->name),
                Xml::decode($person->email),
                Xml::decode($person->uri),
            ));
        }

        // Links
        $links = Xml::links($this->xml);
        $this->linkToSource = $links->whereIn('rel', ['none', 'alternate'])->first();
        $this->linkToFeed = $links->where('rel', 'self')->first();

        // Entries
        $this->entries = collect();
        $namespaces = $this->getXmlNamespaces();
        foreach ($this->xml->entry as $item) {
            $entry = new Entry;
            $entry->initialize($item, $namespaces);
            $this->entries->push($entry);
        }

        // Identifier
        $this->identifier = $this->generateIdentifier($this->xml->id);
    }

    /**
     * What type of feed is this?
     *
     * @return string
     */
    public function getVariant(): string
    {
        return Variants::ATOM100;
    }
}
