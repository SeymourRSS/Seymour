<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\MarkArticleAsUnread;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkArticleAsUnreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_marks_articles_as_unread()
    {
        $article = Article::factory()->read()->create();
        $this->assertTrue($article->has_been_read);

        $action = MarkArticleAsUnread::execute([
            'article' => $article,
        ]);

        $this->assertTrue($action->completed());
        $this->assertFalse($action->article->has_been_read);
    }

    /** @test */
    public function it_requires_an_article()
    {
        $article = Article::factory()->read()->create();
        $this->assertTrue($article->has_been_read);

        $action = MarkArticleAsUnread::execute();

        $this->assertFalse($action->completed());
        $this->assertTrue($article->fresh()->has_been_read);
    }
}
