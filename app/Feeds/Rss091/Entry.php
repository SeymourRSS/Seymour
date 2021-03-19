<?php

namespace App\Feeds\Rss091;

use App\Feeds\Entry as ParentEntry;
use App\Feeds\Variants;
use App\Utilities\Str;
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
        $this->title = Xml::decode($this->xml->title);

        // Categories
        $categories = collect();
        foreach ($this->xml->category as $category) {
            $label = Xml::decode($category);

            if (empty($label)) {
                continue;
            }

            // If the category contains a slash it represents a hierarchy
            // https://www.rssboard.org/rss-0-9-2#ltcategorygtSubelementOfLtitemgt
            // We won't automatically capitalize it.
            if (! Str::contains($label, '/')) {
                $label = ucwords($label);
            }

            $categories->push($label);
        }
        if ($categories->filter()->isNotEmpty()) {
            $this->setExtra('categories', $categories->filter()->toArray());
        }

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
        return Variants::RSS091;
    }
}
