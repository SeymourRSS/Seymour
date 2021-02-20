<?php

namespace App\Feeds;

use Illuminate\Contracts\Support\Arrayable;

class Author implements Arrayable
{
    /**
     * Create a new instance of the Author class.
     *
     * @param string $name
     * @param string $email
     * @param string $uri
     */
    public function __construct(string $name, string $email = '', string $uri = '')
    {
        $this->name = $name;
        $this->email = $email;
        $this->uri = $uri;
    }

    /**
     * @var string
     */
    protected $email = '';

    /**
     * Get the author's email, if available.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @var string
     */
    protected $name = '';

    /**
     * The author's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @var string
     */
    protected $uri = '';

    /**
     * Get the author's URI, if available.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Generate an array representation of this author.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'uri' => $this->uri,
        ];
    }
}
