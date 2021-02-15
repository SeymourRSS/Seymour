<?php

namespace App\Feeds;

use App\Feeds\Feed;
use App\Feeds\Simulator;
use Exception;

class Variants
{
    const RSS090 = 'RSS 0.90';
    const RSS091 = 'RSS 0.91';
    const RSS100 = 'RSS 1.00';
    const RSS200 = 'RSS 2.00';
    const ATOM100 = 'ATOM 1.00';

    /**
     * Create a new feed class from a variant name.
     *
     * @param string $variant
     * @return Feed
     */
    public static function make($variant)
    {
        switch ($variant) {
            case self::RSS090:
                return new \App\Feeds\Rss090\Feed;
                break;

            case self::RSS091:
                return new \App\Feeds\Rss091\Feed;
                break;

            case self::RSS100:
                return new \App\Feeds\Rss100\Feed;
                break;

            case self::RSS200:
                return new \App\Feeds\Rss200\Feed;
                break;

            case self::ATOM100:
                return new \App\Feeds\Atom100\Feed;
                break;

            default:
                throw new Exception("Unknown feed variant");
                break;
        }
    }

    /**
     * Create a new fake feed class from a variant name.
     *
     * @param string $variant
     * @param Simulator $simulator
     * @return FakeFeed
     */
    public static function fake($variant, Simulator $simulator)
    {
        switch ($variant) {
            case self::RSS090:
                return new \App\Feeds\Rss090\Fake($simulator);
                break;

            case self::RSS091:
                return new \App\Feeds\Rss091\Fake($simulator);
                break;

            case self::RSS100:
                return new \App\Feeds\Rss100\Fake($simulator);
                break;

            case self::RSS200:
                return new \App\Feeds\Rss200\Fake($simulator);
                break;

            case self::ATOM100:
                return new \App\Feeds\Atom100\Fake($simulator);
                break;

            default:
                throw new Exception("Unknown feed variant");
                break;
        }
    }
}
