<?php

namespace Tests\Unit\Feeds;

use App\Feeds\Author;
use App\Feeds\Reader;
use App\Feeds\Variants;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RealWorldFeedReaderTest extends TestCase
{
    /** @test */
    public function it_reads_the_nstaaf_feed()
    {
        Reader::fake('nstaaf.xml');
        $result = Reader::fetch('nstaaf.xml');
        $feed = $result->feed;

        $this->assertEquals(Variants::RSS200, $feed->getVariant());
        $this->assertCount(10, $feed->getEntries());
        $this->assertEquals('No Such Thing As A Fish', $feed->getTitle());
        $this->assertEquals('https://audioboom.com/channel/nosuchthingasafish', $feed->getLinkToSource());
        $this->assertEquals('© 2014 Quite Interesting Ltd', $feed->getRights());

        $firstEntry = $feed->getEntries()->first();
        $itunes = $firstEntry->getExtra('itunes');

        $expectedEntrySummary = <<<TEXT
        Dan, Anna, Andrew and James discuss burglary tools, hornet tales, and infinite tiles.

        Visit nosuchthingasafish.com for news about live shows, merchandise and more episodes.
        TEXT;

        $this->assertEquals('348: No Such Thing As Infinite Toilet Paper', $firstEntry->getTitle());
        $this->assertEquals('https://audioboom.com/posts/7732700', $firstEntry->getLinkToSource());
        $this->assertEquals($expectedEntrySummary, $firstEntry->getSummary());
        $this->assertEquals('tag:audioboom.com,2020-11-18:/posts/7732700', $firstEntry->getIdentifier());
        $this->assertEquals('348', $itunes['episode']);
        $this->assertEquals('No Such Thing As Infinite Toilet Paper', $itunes['title']);
        $this->assertEquals('3406', $itunes['duration']);
        $this->assertEquals('no', $itunes['explicit']);
        $this->assertEquals('full', $itunes['episodeType']);
        $this->assertEquals('No Such Thing As A Fish', $itunes['author']);
    }

    /** @test */
    public function it_reads_the_mullenweg_rss_feed()
    {
        Reader::fake('mullenweg.xml');
        $result = Reader::fetch('mullenweg.xml');
        $feed = $result->feed;
        $timestamp = Carbon::parse('Mon, 08 Feb 2021 18:04:26 +0000');

        $this->assertEquals(Variants::RSS200, $feed->getVariant());
        $this->assertCount(5, $feed->getEntries());
        $this->assertTrue($feed->getTimestamp()->eq($timestamp));
        $this->assertEquals('https://ma.tt', $feed->getLinkToSource());
        $this->assertEquals('Matt Mullenweg', $feed->getTitle());
        $this->assertEquals('Unlucky in Cards', $feed->getSubtitle());

        $expectedEntrySummary = <<<SUMMARY
        Excited to welcome Parse.ly to the Automattic family, in an acquisition that&#8217;s closing today. They&#8217;ll be joining our enterprise group, WPVIP. The deal has been nicely covered in the Wall Street Journal and Axios. As a bonus, here&#8217;s Parse.ly co-founder Andrew Montalenti&#8217;s first comment on this blog, in 2012. Great article, Matt. I wrote about [&#8230;]
        SUMMARY;

        $expectedEntryContent = <<<CONTENT
        <p>Excited to <a href="https://blog.parse.ly/post/9995/wpvip-acquisition/">welcome Parse.ly</a> to the <a href="https://automattic.com/">Automattic</a> family, in an acquisition that&rsquo;s closing today. They&rsquo;ll be <a href="https://wpvip.com/2021/02/08/parsely-acquisition/">joining our enterprise group, WPVIP</a>. The deal has been nicely covered in <a href="https://www.wsj.com/articles/wordpress-vip-buying-content-analytics-firm-parse-ly-11612788609">the Wall Street Journal</a> and <a href="https://www.axios.com/wordpress-vip-acquiring-content-analytics-company-parsely-f4b9cc5f-55a9-49db-a8c5-dd3759b7e8e9.html">Axios</a>. As a bonus, here&rsquo;s Parse.ly co-founder Andrew Montalenti&rsquo;s <a href="https://ma.tt/2012/09/future-of-work/#comment-568316">first comment on this blog</a>, in 2012.</p><blockquote class="wp-block-quote"><p>Great article, Matt. I wrote about this on my blog &mdash; Fully Distributed Teams: Are They Viable?</p><p><a href="http://www.pixelmonkey.org/2012/05/14/distributed-teams">http://www.pixelmonkey.org/2012/05/14/distributed-teams</a></p><p>In it, I drew the distinction between &ldquo;horizontally scaled&rdquo; teams, in which physical offices are connected to remote workers via satellite (home or commercial) offices, and &ldquo;fully distributed&rdquo; teams where, as you said, &ldquo;the creative center and soul of the organization on the internet, and not in an office.&rdquo;</p><p>At Parse.ly, we&rsquo;re only a couple years old but have been operating on the distributed team model, with ~13 fully distributed employees, and it&rsquo;s working well. Always glad to hear stories about how Automattic has scaled it to 10X our size.</p><p>And, likewise, we blow some of our office space savings on camaraderie-building retreats; our most recent one was in New York, see [<a href="http://www.flickr.com/photos/amontalenti/sets/72157629938809778/">here</a>] and [<a href="http://www.flickr.com/photos/amontalenti/sets/72157630060466656/">here</a>.]</p></blockquote>
        CONTENT;

        $firstEntry = $feed->getEntries()->first();
        $this->assertEquals($expectedEntrySummary, $firstEntry->getSummary());
        $this->assertEquals($expectedEntryContent, $firstEntry->getContent());
        $this->assertEquals('https://ma.tt/?p=53692', $firstEntry->getIdentifier());
        $this->assertEquals('Parse.ly & Automattic', $firstEntry->getTitle());
        $this->assertEquals('https://ma.tt/2021/02/parse-ly-automattic/', $firstEntry->getLinkToSource());
    }

    /** @test */
    public function it_reads_the_ferriss_atom_feed()
    {
        Reader::fake('ferriss.xml');
        $result = Reader::fetch('ferriss.xml');
        $feed = $result->feed;
        $timestamp = Carbon::parse('2021-02-11T20:57:57Z');
        $author = new Author('Tim Ferriss');

        $this->assertEquals(Variants::ATOM100, $feed->getVariant());
        $this->assertCount(10, $feed->getEntries());
        $this->assertEquals('The Blog of Author Tim Ferriss', $feed->getTitle());
        $this->assertEquals("Tim Ferriss's 4-Hour Workweek and Lifestyle Design Blog. Tim is an author of 5 #1 NYT/WSJ bestsellers, investor (FB, Uber, Twitter, 50+ more), and host of The Tim Ferriss Show podcast (400M+ downloads)", $feed->getSubtitle());
        $this->assertTrue($feed->getTimestamp()->eq($timestamp));
        $this->assertEquals('https://tim.blog/feed/atom/', $feed->getIdentifier());
        $this->assertEquals('https://tim.blog/feed/atom/', $feed->getLinkToFeed());
        $this->assertEquals('https://tim.blog', $feed->getLinkToSource());

        $firstEntry = $feed->getEntries()->first();
        $entryTimestamp = Carbon::parse('2021-02-11T20:57:57Z');
        $this->assertEquals($author, $firstEntry->getAuthors()->first());
        $this->assertEquals('https://tim.blog/?p=54644', $firstEntry->getIdentifier());
        $this->assertStringEqualsFile(base_path('tests/_examples/ferriss_entry_content.html'), $firstEntry->getContent() . "\n");
        $this->assertEquals('https://tim.blog/2021/02/10/joyce-carol-oates/', $firstEntry->getLinkToSource());
        $this->assertEquals('Interview with Joyce Carol Oates on The Tim Ferriss Show podcast.', $firstEntry->getSummary());
        $this->assertTrue($firstEntry->getTimestamp()->eq($entryTimestamp));
        $this->assertEquals('Joyce Carol Oates — A Writing Icon on Creative Process and Creative Living (#497)', $firstEntry->getTitle());
    }

    /** @test */
    public function it_reads_the_popova_rss_feed()
    {
        Reader::fake('popova.xml');
        $result = Reader::fetch('popova.xml');
        $feed = $result->feed;
        $timestamp = Carbon::parse('Thu, 25 Feb 2021 04:04:27 +0000');

        $this->assertEquals(Variants::RSS200, $feed->getVariant());
        $this->assertCount(10, $feed->getEntries());
        $this->assertEquals('Brain Pickings', $feed->getTitle());
        $this->assertEquals('An inventory of the meaningful life.', $feed->getSubtitle());
        $this->assertTrue($feed->getTimestamp()->eq($timestamp));
        $this->assertEquals('1edf64a20ea6cf9286e0a2231cdfe18458aefcfc', $feed->getIdentifier());
        $this->assertEmpty($feed->getLinkToFeed());
        $this->assertEquals('https://www.brainpickings.org', $feed->getLinkToSource());

        $firstEntry = $feed->getEntries()->first();
        $entryTimestamp = Carbon::parse('Mon, 22 Feb 2021 16:55:44 +0000');
        $this->assertTrue($firstEntry->getAuthors()->isEmpty());
        $this->assertEquals('https://www.brainpickings.org/?p=72784', $firstEntry->getIdentifier());
        $this->assertStringEqualsFile(base_path('tests/_examples/popova_entry_content.html'), $firstEntry->getContent() . "\n");
        $this->assertEquals('https://www.brainpickings.org/2021/02/22/mandelbrot-fractals-chaos/', $firstEntry->getLinkToSource());
        $this->assertEquals('"In the mind\'s eye, a fractal is a way of seeing infinity."', $firstEntry->getSummary());
        $this->assertTrue($firstEntry->getTimestamp()->eq($entryTimestamp));
        $this->assertEquals('The Pattern Inside the Pattern: Fractals, the Hidden Order Beneath Chaos, and the Story of the Refugee Who Revolutionized the Mathematics of Reality', $firstEntry->getTitle());
        $this->assertEquals(['culture', 'science', 'Benoit Mandelbrot', 'James Gleick'], $firstEntry->getExtra('categories'));
    }
}
