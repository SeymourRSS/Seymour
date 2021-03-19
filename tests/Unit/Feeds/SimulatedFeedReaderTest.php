<?php

namespace Tests\Unit\Feeds;

use App\Feeds\Author;
use App\Feeds\Reader;
use App\Feeds\Simulator;
use App\Feeds\Variants;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SimulatedFeedReaderTest extends TestCase
{
    /** @test */
    public function it_fetches_feeds_from_a_url()
    {
        $fake = Simulator::make()->withEntry()->as(Variants::RSS090);
        Reader::fake($fake);
        $result = Reader::fetch($fake->getUrl());

        $this->assertInstanceOf(\App\Feeds\Rss090\Feed::class, $result->feed);
    }

    /** @test */
    public function it_catches_invalid_feeds()
    {
        Reader::fake('example.com/feed', 'invalid.xml');
        $result = Reader::fetch('example.com/feed');

        $this->assertTrue($result->hasFailed());
        $this->assertNull($result->feed);
    }

    /** @test */
    public function it_catches_unknown_feeds()
    {
        Reader::fake('example.com/feed', 'unknown.rss');
        $result = Reader::fetch('example.com/feed');

        $this->assertTrue($result->hasFailed());
        $this->assertNull($result->feed);
    }

    /** @test */
    public function it_handles_http_failures()
    {
        Http::fake(['example.com/rss' => Http::response([], 404)]);
        $result = Reader::fetch('example.com/rss');

        $this->assertTrue($result->hasFailed());
        $this->assertNull($result->feed);
    }

    /** @test */
    public function it_reads_rss090_feeds()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12);
        Carbon::setTestNow($knownDate);
        $fake = Simulator::make([
                'title' => 'Example Feed',
                'subtitle' => 'This is RSS 0.90',
                'identifier' => '0123456789',
                'imageUrl' => 'https://picsum.photos/200/300',
                'linkToSource' => 'example.com',
                'timestamp' => $knownDate,
            ])
            ->withEntry([
                'linkToSource' => 'example.com/link',
                'title' => 'Example Entry Title',
            ])
            ->withEntry([
                'linkToSource' => 'example.com/link/2',
                'title' => 'Example Entry Title 2',
            ])
            ->as(Variants::RSS090);
        Reader::fake($fake);

        $result = Reader::fetch($fake->getUrl());

        $feed = $result->feed;
        $this->assertEquals('Example Feed', $feed->getTitle());
        $this->assertEquals('This is RSS 0.90', $feed->getSubtitle());
        $this->assertNotEmpty($feed->getIdentifier());
        $this->assertEquals('example.com', $feed->getLinkToSource());
        $this->assertNull($feed->getTimestamp());
        $this->assertEquals('example.com', $feed->getExtra('image.link'));
        $this->assertEquals('https://picsum.photos/200/300', $feed->getExtra('image.url'));

        $entries = $feed->getEntries();
        $this->assertCount(2, $entries);
        $this->assertNotEmpty($entries[0]->getIdentifier());
        $this->assertEquals('example.com/link', $entries[0]->getLinkToSource());
        $this->assertEquals('Example Entry Title', $entries[0]->getTitle());
        $this->assertNotEmpty($entries[1]->getIdentifier());
        $this->assertEquals('example.com/link/2', $entries[1]->getLinkToSource());
        $this->assertEquals('Example Entry Title 2', $entries[1]->getTitle());
    }

    /** @test */
    public function it_reads_rss091_feeds()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12);
        Carbon::setTestNow($knownDate);
        $fake = Simulator::make([
                'identifier' => '0123456789',
                'imageUrl' => 'https://picsum.photos/200/300',
                'linkToSource' => 'example.com',
                'rights' => 'copyright',
                'subtitle' => 'This is RSS 0.91',
                'title' => 'Example Feed',
                'timestamp' => $knownDate,
            ])
            ->withEntry([
                'categories' => ['cat1', 'cat2'],
                'linkToSource' => 'example.com/link',
                'title' => 'Example Entry Title',
                'summary' => 'Summary 1',
            ])
            ->withEntry([
                'linkToSource' => 'example.com/link/2',
                'title' => 'Example Entry Title 2',
                'summary' => 'Summary 2'
            ])
            ->as(Variants::RSS091);
        Reader::fake($fake);

        $result = Reader::fetch($fake->getUrl());

        $feed = $result->feed;
        $this->assertNotEmpty($feed->getIdentifier());
        $this->assertEquals('example.com', $feed->getLinkToSource());
        $this->assertEquals('copyright', $feed->getRights());
        $this->assertEquals('This is RSS 0.91', $feed->getSubtitle());
        $this->assertTrue($knownDate->equalTo($feed->getTimestamp()));
        $this->assertEquals('Example Feed', $feed->getTitle());
        $this->assertEquals('example.com', $feed->getExtra('image.link'));
        $this->assertEquals('https://picsum.photos/200/300', $feed->getExtra('image.url'));

        $entries = $feed->getEntries();
        $this->assertCount(2, $entries);
        $this->assertNotEmpty($entries[0]->getIdentifier());
        $this->assertEquals(['Cat1', 'Cat2'], $entries[0]->getExtra('categories'));
        $this->assertEquals('example.com/link', $entries[0]->getLinkToSource());
        $this->assertEquals('Summary 1', $entries[0]->getSummary());
        $this->assertEquals('Example Entry Title', $entries[0]->getTitle());
        $this->assertNotEmpty($entries[1]->getIdentifier());
        $this->assertEquals('example.com/link/2', $entries[1]->getLinkToSource());
        $this->assertEquals('Summary 2', $entries[1]->getSummary());
        $this->assertEquals('Example Entry Title 2', $entries[1]->getTitle());
    }

    /** @test */
    public function it_reads_rss100_feeds()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12);
        Carbon::setTestNow($knownDate);
        $fake = Simulator::make([
            'identifier' => '0123456789',
            'linkToSource' => 'example.com',
            'rights' => 'copyright',
            'subtitle' => 'This is RSS 1.0',
            'title' => 'Example Feed',
            'timestamp' => $knownDate,
        ])
            ->withEntry([
                'linkToSource' => 'example.com/link',
                'title' => 'Example Entry Title',
                'summary' => 'Summary 1',
            ])
            ->withEntry([
                'linkToSource' => 'example.com/link/2',
                'title' => 'Example Entry Title 2',
                'summary' => 'Summary 2'
            ])
            ->as(Variants::RSS100);
        Reader::fake($fake);

        $result = Reader::fetch($fake->getUrl());

        $feed = $result->feed;
        $this->assertNotEmpty($feed->getIdentifier());
        $this->assertEquals('example.com', $feed->getLinkToSource());
        $this->assertEquals('This is RSS 1.0', $feed->getSubtitle());
        $this->assertNull($feed->getTimestamp());
        $this->assertEquals('Example Feed', $feed->getTitle());
        $entries = $feed->getEntries();
        $this->assertCount(2, $entries);
        $this->assertNotEmpty($entries[0]->getIdentifier());
        $this->assertEquals('example.com/link', $entries[0]->getLinkToSource());
        $this->assertEquals('Summary 1', $entries[0]->getSummary());
        $this->assertEquals('Example Entry Title', $entries[0]->getTitle());
        $this->assertNotEmpty($entries[1]->getIdentifier());
        $this->assertEquals('example.com/link/2', $entries[1]->getLinkToSource());
        $this->assertEquals('Summary 2', $entries[1]->getSummary());
        $this->assertEquals('Example Entry Title 2', $entries[1]->getTitle());
    }

    /** @test */
    public function it_reads_rss200_feeds()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12);
        Carbon::setTestNow($knownDate);
        $author = new Author('Grace Hopper', 'grace@example.com', 'example.com');
        $fake = Simulator::make([
            'categories' => ['cat1', 'cat2'],
            'identifier' => '0123456789',
            'imageUrl' => 'https://picsum.photos/200/300',
            'linkToSource' => 'example.com',
            'rights' => 'copyright',
            'subtitle' => 'This is RSS 2.0',
            'title' => 'Example Feed',
            'timestamp' => $knownDate,
        ])
            ->withEntry([
                'categories' => ['cat3', 'cat4'],
                'identifier' => 'guid1',
                'linkToSource' => 'example.com/link',
                'title' => 'Example Entry Title',
                'summary' => 'Summary 1',
                'content' => 'Content 1',
            ])
            ->withEntry([
                'authors' => [$author],
                'identifier' => 'guid2',
                'linkToSource' => 'example.com/link/2',
                'title' => 'Example Entry Title 2',
                'summary' => 'Summary 2',
                'content' => 'Content 2',
            ])
            ->as(Variants::RSS200);
        Reader::fake($fake);

        $result = Reader::fetch($fake->getUrl());

        $feed = $result->feed;
        $this->assertNotEmpty($feed->getIdentifier());
        $this->assertEquals('example.com', $feed->getLinkToSource());
        $this->assertEquals('copyright', $feed->getRights());
        $this->assertEquals('This is RSS 2.0', $feed->getSubtitle());
        $this->assertTrue($knownDate->equalTo($feed->getTimestamp()));
        $this->assertEquals('Example Feed', $feed->getTitle());
        $this->assertEquals(['Cat1', 'Cat2'], $feed->getExtra('categories'));
        $this->assertEquals('example.com', $feed->getExtra('image.link'));
        $this->assertEquals('https://picsum.photos/200/300', $feed->getExtra('image.url'));
        $entries = $feed->getEntries();
        $this->assertCount(2, $entries);
        $this->assertEmpty($entries[0]->getAuthors());
        $this->assertEmpty($entries[0]->getContent());
        $this->assertEquals('guid1', $entries[0]->getIdentifier());
        $this->assertEquals('example.com/link', $entries[0]->getLinkToSource());
        $this->assertEquals('Summary 1', $entries[0]->getSummary());
        $this->assertEquals('Example Entry Title', $entries[0]->getTitle());
        $this->assertEquals(['Cat3', 'Cat4'], $entries[0]->getExtra('categories'));
        $this->assertEmpty($entries[1]->getContent());
        $this->assertInstanceOf(Author::class, $entries[1]->getAuthors()->first());
        $this->assertEquals('guid2', $entries[1]->getIdentifier());
        $this->assertEquals('example.com/link/2', $entries[1]->getLinkToSource());
        $this->assertEquals('Summary 2', $entries[1]->getSummary());
        $this->assertEquals('Example Entry Title 2', $entries[1]->getTitle());
        $this->assertEquals('grace@example.com', $entries[1]->getAuthors()->first()->getName());
    }

    /** @test */
    public function it_reads_atom_100_feeds()
    {
        $knownDate = Carbon::create(2020, 1, 1, 12);
        $firstEntryDate = $knownDate->copy()->subDays(1);
        $secondEntryDate = $knownDate->copy()->subDays(2);
        $author = new Author('Grace Hopper', 'grace@example.com', 'example.com');
        Carbon::setTestNow($knownDate);
        $fake = Simulator::make([
            'authors' => [$author],
            'categories' => ['cat1', 'cat2'],
            'identifier' => '0123456789',
            'imageUrl' => 'https://picsum.photos/200/300',
            'linkToSource' => 'example.com',
            'linkToFeed' => 'example.com/feed',
            'rights' => 'copyright',
            'subtitle' => 'This is Atom 1.0',
            'title' => 'Example Feed',
            'timestamp' => $knownDate,
        ])
            ->withEntry([
                'authors' => [$author],
                'categories' => ['cat3', 'cat4'],
                'content' => 'Content 1',
                'identifier' => 'guid1',
                'linkToSource' => 'example.com/link',
                'title' => 'Example Entry Title',
                'summary' => 'Summary 1',
                'timestamp' => $firstEntryDate,
            ])
            ->withEntry([
                'content' => 'Content 2',
                'identifier' => 'guid2',
                'linkToSource' => 'example.com/link/2',
                'title' => 'Example Entry Title 2',
                'summary' => 'Summary 2',
                'timestamp' => $secondEntryDate,
            ])
            ->as(Variants::ATOM100);
        Reader::fake($fake);

        $result = Reader::fetch($fake->getUrl());

        $feed = $result->feed;
        $this->assertNotEmpty($feed->getIdentifier());
        $this->assertEquals(['Cat1', 'Cat2'], $feed->getExtra('categories'));
        $this->assertEquals('https://picsum.photos/200/300', $feed->getExtra('image.url'));
        $this->assertEquals('example.com', $feed->getLinkToSource());
        $this->assertEquals('This is Atom 1.0', $feed->getSubtitle());
        $this->assertTrue($knownDate->equalTo($feed->getTimestamp()));
        $this->assertEquals('Example Feed', $feed->getTitle());
        $this->assertEquals('copyright', $feed->getRights());
        $this->assertInstanceOf(Author::class, $feed->getAuthors()->first());
        $this->assertEquals('Grace Hopper', $feed->getAuthors()->first()->getName());
        $this->assertEquals('grace@example.com', $feed->getAuthors()->first()->getEmail());
        $this->assertEquals('example.com', $feed->getAuthors()->first()->getUri());

        $entries = $feed->getEntries();
        $this->assertCount(2, $entries);

        $this->assertEquals(['Cat3', 'Cat4'], $entries[0]->getExtra('categories'));
        $this->assertEquals('guid1', $entries[0]->getIdentifier());
        $this->assertEquals('example.com/link', $entries[0]->getLinkToSource());
        $this->assertEquals('Summary 1', $entries[0]->getSummary());
        $this->assertTrue($firstEntryDate->equalTo($entries[0]->getTimestamp()));
        $this->assertEquals('Example Entry Title', $entries[0]->getTitle());
        $this->assertInstanceOf(Author::class, $entries[0]->getAuthors()->first());
        $this->assertEquals('Grace Hopper', $entries[0]->getAuthors()->first()->getName());
        $this->assertEquals('grace@example.com', $entries[0]->getAuthors()->first()->getEmail());
        $this->assertEquals('example.com', $entries[0]->getAuthors()->first()->getUri());

        $this->assertEquals('guid2', $entries[1]->getIdentifier());
        $this->assertEquals('example.com/link/2', $entries[1]->getLinkToSource());
        $this->assertEquals('Summary 2', $entries[1]->getSummary());
        $this->assertTrue($secondEntryDate->equalTo($entries[1]->getTimestamp()));
        $this->assertEquals('Example Entry Title 2', $entries[1]->getTitle());
        $this->assertEmpty($entries[1]->getAuthors());
    }
}
