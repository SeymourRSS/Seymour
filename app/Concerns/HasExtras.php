<?php

namespace App\Concerns;

use App\Utilities\Arr;

trait HasExtras
{
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
        $this->extra[$key] = $value;
    }
}
