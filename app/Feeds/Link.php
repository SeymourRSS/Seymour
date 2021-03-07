<?php

namespace App\Feeds;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

class Link implements ArrayAccess, Arrayable
{
    /**
     * @var array
     */
    public const ATTRIBUTES = [
        'href',
        'hreflang',
        'length',
        'rel',
        'title',
        'type'
    ];

    /**
     * @var string
     */
    protected $href = '';

    /**
     * Get the href attribute value.
     *
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @var string
     */
    protected $hreflang = '';

    /**
     * Get the hreflang attribute value.
     *
     * @return string
     */
    public function getHrefLang(): string
    {
        return $this->hreflang;
    }

    /**
     * @var int
     */
    protected $length = 0;

    /**
     * Get the length attribute value.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @var string
     */
    protected $rel = 'none';

    /**
     * Get the rel attribute value.
     *
     * @return string
     */
    public function getRel(): string
    {
        return $this->rel;
    }

    /**
     * @var string
     */
    protected $title = '';

    /**
     * Get the title attribute value.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @var string
     */
    protected $type = '';

    /**
     * Get the type attribute value.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Create a new Link class instance from an array of attributes.
     *
     * @param array $attributes
     * @return self
     */
    public static function fromArray($attributes = []): self
    {
        $link = new static();

        foreach (self::ATTRIBUTES as $key) {
            $link->$key = array_key_exists($key, $attributes)
                ? $attributes[$key]
                : $link->$key;
        }

        return $link;
    }

    /**
     * Does a certain property exist?
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return in_array($offset, self::ATTRIBUTES);
    }

    /**
     * Retrieve the value of a property by key.
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset)
            ? $this->{$offset}
            : null;
    }

    /**
     * Set a value based on a provided key.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            $this->{$offset} = $value;
        }
    }

    /**
     * Remove a value based on an offset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        // Will we need this?
    }

    /**
     * Generate a string representation of this link.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->href;
    }

    /**
     * Generate an array representation of this link.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'href' => $this->href,
            'hreflang' => $this->hreflang,
            'length' => $this->length,
            'rel' => $this->rel,
            'title' => $this->title,
            'type' => $this->type,
        ];
    }
}
