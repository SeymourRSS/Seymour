<?php

namespace App\Concerns;

use App\Utilities\Arr;

trait HasExtras
{
    /**
     * Initialize the 'has extras' trait for an instance.
     *
     * @return void
     */
    public function initializeHasExtras()
    {
        if (!isset($this->casts['extra'])) {
            $this->casts['extra'] = 'array';
        }
    }

    /**
     * Retrieve a value from the 'extra' field.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getExtra($key = null, $default = null): mixed
    {
        if ($key) {
            return Arr::get($this->extra, $key, $default);
        }

        return $this->extra;
    }

    /**
     * Record a value in the 'extra' field.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setExtra($key, $value): void
    {
        $extra = $this->extra;
        $extra[$key] = $value;
        $this->extra = $extra;
    }
}
