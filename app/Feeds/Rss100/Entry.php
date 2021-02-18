<?php

namespace App\Feeds\Rss100;

use App\Feeds\Entry as ParentEntry;
use App\Feeds\Variants;
use App\Utilities\Xml;
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
        $this->summary = Xml::decode($this->xml->description);
        $this->title = Xml::decode($this->xml->title);

        // Links
        $this->setExtra('links', Xml::links($this->xml));
        $this->linkToSource = $this->getExtra('links')
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
        return Variants::RSS100;
    }
}
