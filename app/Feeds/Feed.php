<?php

namespace App\Feeds;

use App\Utilities\Xml;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use SimpleXMLElement;

abstract class Feed
{
    /**
     * @var Collection
     */
    protected $authors;

    /**
     * Get the feed authors.
     *
     * @return Collection
     */
    public function getAuthors(): Collection
    {
        return empty($this->authors) ? collect() : $this->authors;
    }

    /**
     * @var string
     */
    protected $checksum;

    /**
     * Set the content checksum.
     *
     * @return void
     */
    protected function setChecksum(): void
    {
        $this->checksum = sha1((string)$this->xml);
    }

    /**
     * @var Collection
     */
    protected $entries;

    /**
     * Get the feed entries.
     *
     * @return Collection
     */
    public function getEntries(): Collection
    {
        return empty($this->entries) ? collect() : $this->entries;
    }

    /**
     * @var string
     */
    public $identifier = '';

    /**
     * Retrieve the feed identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Use entry attributes to generate a seemingly unique identifier.
     *
     * @param SimpleXMLElement|null $xml
     * @return string
     */
    protected function generateIdentifier(SimpleXMLElement $xml = null): string
    {
        $identifier = Xml::decode($xml);

        return empty($identifier)
            ? sha1($this->getTitle() . $this->getLinkToSource())
            : $identifier;
    }

    /**
     * @var string
     */
    protected $license;

    /**
     * Get the feed license.
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @var string
     */
    protected $linkToFeed = '';

    /**
     * Retrieve the link to the feed.
     *
     * @return string
     */
    public function getLinkToFeed(): string
    {
        return $this->linkToFeed;
    }

    /**
     * @var string
     */
    protected $linkToSource = '';

    /**
     * Retrieve the link to the originating source of the feed.
     *
     * @return string
     */
    public function getLinkToSource(): string
    {
        return $this->linkToSource;
    }

    /**
     * Get the namespaces defined in the Feed XML.
     *
     * @return array
     */
    public function getXmlNamespaces()
    {
        return $this->xml->getNamespaces(true);
    }

    /**
     * @var string
     */
    protected $rights;

    /**
     * Get the feed copyright.
     *
     * @return string
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * @var string
     */
    protected $subtitle = '';

    /**
     * Retrieve the feed subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @var string
     */
    protected $title = '';

    /**
     * Retrieve the feed title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @var Carbon|null
     */
    protected $timestamp = null;

    /**
     * Get the feed timestamp, if available.
     *
     * @return Carbon|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * Retrieve the feed's raw XML.
     *
     * @return SimpleXMLElement
     */
    public function getXml(): SimpleXMLElement
    {
        return $this->xml;
    }

    /**
     * Populate the feed instance with values from the XML.
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    public function initialize($xml): void
    {
        $this->xml = $xml;
        $this->setChecksum();
        $this->readDefaultNamespace();
    }

    /**
     * Read the values in the feed's default XML namespace.
     *
     * @return void
     */
    abstract public function readDefaultNamespace(): void;

    /**
     * What type of feed is this?
     *
     * @return string
     */
    abstract public function getVariant(): string;
}
