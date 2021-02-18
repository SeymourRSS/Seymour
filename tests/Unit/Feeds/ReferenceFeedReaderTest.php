<?php

namespace Tests\Unit\Feeds;

use App\Feeds\Reader;
use App\Feeds\Variants;
use Tests\TestCase;

class ReferenceFeedReaderTest extends TestCase
{
    /** @test */
    public function it_reads_the_rss_090_reference_xml()
    {
        Reader::fake('rss090reference.xml');
        $result = Reader::fetch('rss090reference.xml');
        $feed = $result->feed;

        $this->assertEquals(Variants::RSS090, $feed->getVariant());
        $this->assertEquals('Mozilla Dot Org', $feed->getTitle());
        $this->assertEquals('http://www.mozilla.org', $feed->getLinkToSource());
        $this->assertEquals("the Mozilla Organization\n      web site", $feed->getSubtitle());
        $this->assertCount(5, $feed->getEntries());
        $this->assertEquals('New Status Updates', $feed->getEntries()->first()->getTitle());
        $this->assertEquals('http://www.mozilla.org/status/', $feed->getEntries()->first()->getLinkToSource());
        $this->assertNotEmpty($feed->getEntries()->first()->getIdentifier());
    }

    /** @test */
    public function it_reads_the_rss_091_reference_xml()
    {
        Reader::fake('rss091reference.xml');
        $result = Reader::fetch('rss091reference.xml');
        $feed = $result->feed;

        $this->assertEquals(Variants::RSS091, $feed->getVariant());
        $this->assertEquals('WriteTheWeb', $feed->getTitle());
        $this->assertEquals('http://writetheweb.com', $feed->getLinkToSource());
        $this->assertEquals('News for web users that write back', $feed->getSubtitle());
        $this->assertEquals('Copyright 2000, WriteTheWeb team.', $feed->getRights());
        $this->assertCount(6, $feed->getEntries());
        $this->assertEquals('Giving the world a pluggable Gnutella', $feed->getEntries()->first()->getTitle());
        $this->assertEquals('http://writetheweb.com/read.php?item=24', $feed->getEntries()->first()->getLinkToSource());
        $this->assertEquals(
            'WorldOS is a framework on which to build programs that work like Freenet or Gnutella -allowing distributed applications using peer-to-peer routing.',
            $feed->getEntries()->first()->getSummary()
        );
        $this->assertNotEmpty($feed->getEntries()->first()->getIdentifier());
    }

    /** @test */
    public function it_reads_the_rss_100_reference_xml()
    {
        Reader::fake('rss100reference.xml');
        $result = Reader::fetch('rss100reference.xml');
        $feed = $result->feed;

        $this->assertEquals(Variants::RSS100, $feed->getVariant());
        $this->assertEquals('TutorialsPoint', $feed->getTitle());
        $this->assertEquals('http://tutorialspoint.com/rss', $feed->getLinkToSource());
        $this->assertEquals('Online Tutorials and Reference Manuals', $feed->getSubtitle());
        $this->assertCount(2, $feed->getEntries());
        $this->assertEquals('A simple RSS tutorial', $feed->getEntries()->first()->getTitle());
        $this->assertEquals('http://tutorialspoint.com/rss/index.htm', $feed->getEntries()->first()->getLinkToSource());
        $this->assertEquals('Learn RSS in simple and easy steps.', $feed->getEntries()->first()->getSummary());
    }

    /** @test */
    public function it_reads_the_rss_200_reference_xml()
    {
        Reader::fake('rss200reference.xml');
        $result = Reader::fetch('rss200reference.xml');
        $feed = $result->feed;

        $this->assertEquals(Variants::RSS200, $feed->getVariant());
        $this->assertEquals('Scripting News', $feed->getTitle());
        $this->assertEquals('http://www.scripting.com/', $feed->getLinkToSource());
        $this->assertEquals('A weblog about scripting and stuff like that.', $feed->getSubtitle());
        $this->assertEquals('Copyright 1997-2002 Dave Winer', $feed->getRights());
        $this->assertEquals('2002-09-30 11:00:00', $feed->getTimestamp()->toDateTimeString());
        $this->assertCount(9, $feed->getEntries());

        $expectedEntrySummary = <<<TEXT
        "rssflowersalignright"With any luck we should have one or two more days of namespaces stuff here on Scripting News. It feels like it's winding down. Later in the week I'm going to a <a href="http://harvardbusinessonline.hbsp.harvard.edu/b02/en/conferences/conf_detail.jhtml?id=s775stg&pid=144XCF">conference</a> put on by the Harvard Business School. So that should change the topic a bit. The following week I'm off to Colorado for the <a href="http://www.digitalidworld.com/conference/2002/index.php">Digital ID World</a> conference. We had to go through namespaces, and it turns out that weblogs are a great way to work around mail lists that are clogged with <a href="http://www.userland.com/whatIsStopEnergy">stop energy</a>. I think we solved the problem, have reached a consensus, and will be ready to move forward shortly.
        TEXT;

        $this->assertEquals($expectedEntrySummary, $feed->getEntries()->first()->getSummary());
        $this->assertEquals('2002-09-30 01:56:02', $feed->getEntries()->first()->getTimestamp()->toDateTimeString());
        $this->assertEquals(
            'http://scriptingnews.userland.com/backissues/2002/09/29#When:6:56:02PM',
            $feed->getEntries()->first()->getIdentifier()
        );
    }

    /** @test */
    public function it_reads_the_atom_100_reference_xml()
    {
        Reader::fake('atom100reference.xml');
        $result = Reader::fetch('atom100reference.xml');
        $feed = $result->feed;

        $this->assertEquals(Variants::ATOM100, $feed->getVariant());
        $this->assertEquals('Example Feed', $feed->getTitle());
        $this->assertEquals('A subtitle.', $feed->getSubtitle());
        $this->assertEquals('http://example.org/feed/', $feed->getLinkToFeed());
        $this->assertEquals('http://example.org/', $feed->getLinkToSource());
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b91C-0003939e0af6', $feed->getIdentifier());
        $this->assertEquals('2003-12-13 18:30:02', $feed->getTimestamp()->toDateTimeString());
        $this->assertCount(1, $feed->getEntries());
        $this->assertEquals('Atom-Powered Robots Run Amok', $feed->getEntries()->first()->getTitle());
        $this->assertEquals('http://example.org/2003/12/13/atom03', $feed->getEntries()->first()->getLinkToSource());
        $this->assertEquals('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a', $feed->getEntries()->first()->getIdentifier());
        $this->assertEquals('2003-12-13 18:30:02', $feed->getEntries()->first()->getTimestamp()->toDateTimeString());
        $this->assertEquals('Some text.', $feed->getEntries()->first()->getSummary());

        $expectedFeedContent = <<<HTML
        <div xmlns="http://www.w3.org/1999/xhtml"><p>This is the entry content.</p></div>
        HTML;

        $this->assertEquals($expectedFeedContent, $feed->getEntries()->first()->getContent());
        $this->assertEquals('John Doe', $feed->getEntries()->first()->getAuthors()->first()->getName());
        $this->assertEquals('johndoe@example.com', $feed->getEntries()->first()->getAuthors()->first()->getEmail());
    }
}
