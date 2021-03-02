<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\MarkArticleAsRead;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkArticleAsReadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_marks_articles_as_read()
    {
        $article = Article::factory()->create();
        $this->assertFalse($article->has_been_read);

        $action = MarkArticleAsRead::execute([
            'article' => $article,
        ]);

        $this->assertTrue($action->completed());
        $this->assertTrue($action->article->has_been_read);
    }

    /** @test */
    public function it_requires_an_article()
    {
        $article = Article::factory()->create();
        $this->assertFalse($article->has_been_read);

        $action = MarkArticleAsRead::execute();

        $this->assertFalse($action->completed());
        $this->assertFalse($article->fresh()->has_been_read);
    }
}
