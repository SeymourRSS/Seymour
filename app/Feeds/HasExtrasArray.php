<?php

namespace App\Feeds;

use App\Utilities\Arr;

/**
 * Methods for working with an array of 'extras' that may be captured from
 * feed or entry XML but don't have a dedicated place in the Subscription
 * or Article model format.
 */
trait HasExtrasArray
{
    /**
     * @var array
     */
    protected $extra = [];

    /**
     * Retrieve extra information that may have been parsed from the XML.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getExtra($key = null, $default = null): mixed
    {
        if ($key) {
            return data_get($this->extra, $key, $default);
        }

        return $this->extra;
    }

    /**
     * Check to see if a key is available in the extras array.
     *
     * @param string $key
     * @return boolean
     */
    public function hasExtra($key)
    {
        return Arr::has($this->extra, $key);
    }

    /**
     * Record an 'extra' value that was parsed from the XML.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setExtra($key, $value): void
    {
        $this->extra = data_set($this->extra, $key, $value);
    }
}
