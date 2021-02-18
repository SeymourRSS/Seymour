<?php

namespace App\Feeds\Exceptions;

use App\Utilities\Phrase;
use Exception;

class InvalidXmlException extends Exception
{
    /**
     * An array of LibXMLError values retrieved from libxml_get_errors().
     *
     * @var array
     */
    protected $errors = [];

    /**
     * The XML that triggered the exception.
     *
     * @var string
     */
    protected $invalidXml = '';

    /**
     * Create a new invalid xml exception.
     *
     * @param string $url
     * @param array $errors
     */
    public function __construct($xml, array $errors = [])
    {
        parent::__construct(Phrase::ATTEMPTED_INVALID_XML);

        $this->errors = $errors;
        $this->invalidXml = $xml;
    }

    /**
     * Convert the error set to a format that is easier to read.  Borrowed from
     * https://www.php.net/manual/en/function.libxml-get-errors.php
     *
     * @return array
     */
    public function list()
    {
        $list = [];

        foreach ($this->errors as $error) {
            $message = trim($error->message);

            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $list[] = "Warning {$error->code}: {$message} (Line {$error->line})";
                    break;
                case LIBXML_ERR_ERROR:
                    $list[] = "Error {$error->code}: {$message} (Line {$error->line})";
                    break;
                case LIBXML_ERR_FATAL:
                    $list[] = "Fatal Error {$error->code}: {$message} (Line {$error->line})";
                    break;
            }
        }

        return $list;
    }
}
