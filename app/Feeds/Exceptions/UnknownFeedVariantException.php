<?php

namespace App\Feeds\Exceptions;

use App\Utilities\Phrase;
use Exception;

class UnknownFeedVariantException extends Exception
{
    /**
     * The XML that triggered the exception.
     *
     * @var string
     */
    protected $invalidXml = '';

    /**
     * Create a new invalid xml exception.
     *
     * @param array $errors
     */
    public function __construct($xml)
    {
        parent::__construct(Phrase::translate('UNKNOWN_FEED_VARIANT'));

        $this->invalidXml = $xml;
    }
}
