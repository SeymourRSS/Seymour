<?php

namespace App\Utilities;

/**
 * Here we define all of the language strings that are used by the
 * application business logic. Translation will happen in the
 * view templates, using these strings as translation keys.
 */
class Phrase
{
    // Feeds
    public const FEED_HTTP_ERROR = 'There was a problem fetching the content of this feed.';
    public const ATTEMPTED_INVALID_XML = 'Attempted to parse invalid XML.';
    public const UNKNOWN_FEED_VARIANT = 'Unknown feed variant.';

    // Subscriptions
    public const SUBSCRIPTION_CREATED = "You have been subscribed to ':title'";
    public const SUBSCRIPTION_REMOVED = "You have been unsubscribed from ':title'";

    /**
     * Localize a language constant defined in this class. Defers to the
     * translation helper provided by Laravel.
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    public static function translate($key, $replace = [], $locale = null)
    {
        return trans(constant('self::' . $key), $replace, $locale);
    }
}
