<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\RemoveArticle;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_articles()
    {
        $article = Article::factory()->create();

        $action = RemoveArticle::execute([
            'article' => $article,
        ]);

        $this->assertTrue($action->completed());
        $this->assertSoftDeleted($article->fresh());
    }

    /** @test */
    public function it_requires_an_article()
    {
        $article = Article::factory()->create();

        $action = RemoveArticle::execute();

        $this->assertFalse($action->completed());
        $this->assertNull($article->fresh()->deleted_at);
    }
}
