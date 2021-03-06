<?php

namespace App\Feeds;

use App\Feeds\HasExtrasArray;
use App\Feeds\Modules;
use App\Utilities\Xml;
use Illuminate\Support\Collection;
use SimpleXMLElement;

abstract class Entry
{
    use HasExtrasArray;

    /**
     * @var Collection
     */
    protected $authors;

    /**
     * Get the entry's authors.
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
    protected $content = '';

    /**
     * Get the entry's content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * Get the entry's unique identifier;
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
     * @param SimpleXMLElement $xml
     * @return string
     */
    protected function generateIdentifier($xml = null): string
    {
        $identifier = Xml::decode($xml);

        return empty($identifier)
            ? sha1($this->getTitle() . $this->getLinkToSource())
            : $identifier;
    }

    /**
     * @var string
     */
    protected $linkToSource = '';

    /**
     * Get the entry's source link.
     *
     * @return string
     */
    public function getLinkToSource(): string
    {
        return $this->linkToSource;
    }

    /**
     * @var string
     */
    protected $rights = '';

    /**
     * Get the entry's copyright information.
     *
     * @return string
     */
    public function getRights(): string
    {
        return $this->rights;
    }

    /**
     * @var string
     */
    protected $summary = '';

    /**
     * Get the entry's summary.
     *
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @var Carbon|null
     */
    protected $timestamp = null;

    /**
     * Get the entry's timestamp.
     *
     * @return Carbon|null
     */
    public function getTimestamp()
    {
        return $this->timestamp
            ? $this->timestamp->clone()
            : null;
    }

    /**
     * Get the entry's timestamp as a string.
     *
     * @return string
     */
    public function getRawTimestamp()
    {
        return $this->timestamp
            ? $this->timestamp->toIso8601String()
            : '';
    }

    /**
     * @var string
     */
    protected $title = '';

    /**
     * Get the entry's title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * Retrieve the entry's xml.
     *
     * @return SimpleXMLElement
     */
    public function getXml(): SimpleXMLElement
    {
        return $this->xml;
    }

    /**
     * Initialize the entry from a SimpleXMLElement.
     *
     * @param SimpleXMLElement $xml
     * @param array $namespaces
     * @return void
     */
    public function initialize(SimpleXMLElement $xml, array $namespaces): void
    {
        $this->xml = $xml;
        $this->readDefaultNamespace();

        foreach (Modules::available($namespaces) as $key => $name) {
            $method = "read{$name}Namespace";
            if (method_exists($this, $method)) {
                $this->$method($key);
            }
        }
    }

    /**
     * Read the values in the entry's default XML namespace.
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
