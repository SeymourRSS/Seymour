<?php

namespace App\Feeds;

use App\Feeds\Exceptions\InvalidXMLException;
use App\Feeds\Exceptions\UnknownFeedVariantException;
use App\Feeds\FakeFeed;
use App\Feeds\Feed;
use App\Utilities\Phrase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Reader {

    /**
     * @var Feed
     */
    public $feed;

    /**
     * @var boolean
     */
    public $hasHttpError = true;

    /**
     * @var boolean
     */
    public $hasInvalidFeed = true;

    /**
     * @var string
     */
    public $message = '';

    /**
     * Fetch feed content from a URL.
     *
     * @param string $url
     * @return static
     */
    public static function fetch($url)
    {
        $reader = new static;
        $response = Http::get($url);

        if ($response->failed()) {
            $reader->hasHttpError = $response->failed();
            $reader->message = Phrase::FEED_HTTP_ERROR;
            Log::notice("Http Client Error: GET '{$url}' returned a {$response->status()} response.");
            return $reader;
        }

        try {
            $reader->feed = Interpreter::parse($response->body());
        } catch (UnknownFeedVariantException $th) {
            $reader->hasInvalidFeed = true;
            $reader->message = $th->getMessage();
            Log::notice("Unsupported RSS variant: '{$url}' could not be parsed.");
            return $reader;
        } catch (InvalidXMLException $th) {
            $reader->hasInvalidFeed = true;
            $reader->message = $th->getMessage();
            Log::notice("Invalid feed format: '{$url}' could not be parsed.", $th->list());
            return $reader;
        }

        return $reader;
    }

    /**
     * Did the request fail?
     *
     * @return bool
     */
    public function hasFailed()
    {
        return $this->hasInvalidFeed || $this->hasHttpError;
    }

    /**
     * Simulate an HTTP call with fake response data for testing.
     *
     * @param FakeFeed|string $feed
     * @param string $content
     * @return void
     */
    public static function fake($feed, $content = '')
    {
        if ($feed instanceof FakeFeed) {
            Http::fake([
                $feed->getUrl() => Http::response($feed->toString(), 200),
                '*' => Http::response('', 500),
            ]);
        } elseif (is_string($feed)) {

            $content = file_exists(base_path("tests/_examples/{$feed}"))
                ? file_get_contents(base_path("tests/_examples/{$feed}"))
                : $content;

            Http::fake([
                $feed => Http::response($content, 200),
                '*' => Http::response('', 500),
            ]);
        } else {
            Http::fake();
        }
    }
}
