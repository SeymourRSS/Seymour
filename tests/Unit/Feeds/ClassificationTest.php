<?php

namespace Tests\Unit\Feeds;

use App\Feeds\FakeFeed;
use App\Feeds\Interpreter;
use App\Feeds\Reader;
use App\Feeds\Rss090\Rss090;
use App\Feeds\Simulator;
use App\Feeds\Variants;
use Tests\TestCase;

class ClassificationTest extends TestCase
{
    /** @test */
    public function it_classifies_feed_variants()
    {
        $feed = Simulator::make()->withEntry()->as(Variants::RSS090);
        $xml = simplexml_load_string($feed->toString());
        $this->assertEquals(Variants::RSS090, Interpreter::classify($xml));

        $feed = Simulator::make()->withEntry()->as(Variants::RSS091);
        $xml = simplexml_load_string($feed->toString());
        $this->assertEquals(Variants::RSS091, Interpreter::classify($xml));

        $feed = Simulator::make()->withEntry()->as(Variants::RSS100);
        $xml = simplexml_load_string($feed->toString());
        $this->assertEquals(Variants::RSS100, Interpreter::classify($xml));

        $feed = Simulator::make()->withEntry()->as(Variants::RSS200);
        $xml = simplexml_load_string($feed->toString());
        $this->assertEquals(Variants::RSS200, Interpreter::classify($xml));

        $feed = Simulator::make()->withEntry()->as(Variants::ATOM100);
        $xml = simplexml_load_string($feed->toString());
        $this->assertEquals(Variants::ATOM100, Interpreter::classify($xml));
    }
}
