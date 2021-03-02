<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\RecordArticle;
use App\Feeds\Author;
use App\Feeds\Reader;
use App\Feeds\Simulator;
use App\Feeds\Variants;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_articles()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);
        $knownDate = Carbon::create(2020, 1, 1, 12);
        $author = new Author('Grace Hopper', 'grace@example.com', 'example.com');
        $firstEntryDate = $knownDate->copy()->subDays(1);
        Carbon::setTestNow($knownDate);
        $fake = Simulator::make()->withEntry([
            'authors' => [$author],
            'content' => 'Content 1',
            'identifier' => 'guid1',
            'linkToSource' => 'example.com/link',
            'title' => 'Example Entry Title',
            'summary' => 'Summary 1',
            'timestamp' => $firstEntryDate,
        ])->as(Variants::ATOM100);
        Reader::fake($fake);
        $feed = Reader::fetch($fake->getUrl())->feed;

        $action = RecordArticle::execute([
            'entry' => $feed->getEntries()->first(),
            'subscription' => $subscription,
        ]);

        $this->assertTrue($action->completed());
        $this->assertEquals([$author->toArray()], $action->article->getExtra('authors'));
        $this->assertEquals('<p>        Content 1    </p>', $action->article->content);
        $this->assertEquals('guid1', $action->article->identifier);
        $this->assertEquals('example.com/link', $action->article->link_to_source);
        $this->assertEquals('Example Entry Title', $action->article->title);
        $this->assertEquals('Summary 1', $action->article->summary);
        $this->assertTrue($action->article->entry_timestamp->eq($firstEntryDate));
        $expectedLink = [
            'href' => 'example.com/link',
            'hreflang' => '',
            'length' => 0,
            'rel' => 'none',
            'title' => '',
            'type' => '',
        ];
        $this->assertEquals($expectedLink, $action->article->getExtra('links')[0]);
    }
}
