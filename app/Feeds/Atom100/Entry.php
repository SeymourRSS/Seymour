<?php

namespace App\Feeds\Atom100;

use App\Feeds\Author;
use App\Feeds\Entry as ParentEntry;
use App\Feeds\Variants;
use App\Utilities\Xml;
use Illuminate\Support\Carbon;
use SimpleXMLElement;

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
        $this->content = Xml::content($this->xml->content);
        // dd($this->content, $this->xml->content->children());
        $this->summary = Xml::decode($this->xml->summary);
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
        $this->setExtra('links', Xml::links($this->xml));
        $this->linkToSource = $this->getExtra('links')
            ->whereIn('rel', ['none', 'alternate'])
            ->first();

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