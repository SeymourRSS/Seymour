<?php

namespace Tests\Unit\Actions\Feeds;

use App\Actions\Feeds\RestoreArticle;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestoreArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_restores_articles()
    {
        $article = Article::factory()->deleted()->create();
        $this->assertSoftDeleted($article);

        $action = RestoreArticle::execute([
            'article' => $article,
        ]);

        $this->assertTrue($action->completed());
        $this->assertNull($article->fresh()->deleted_at);
    }

    /** @test */
    public function it_requires_an_article()
    {
        $article = Article::factory()->deleted()->create();
        $this->assertSoftDeleted($article);

        $action = RestoreArticle::execute();

        $this->assertFalse($action->completed());
        $this->assertSoftDeleted($article->fresh());
    }
}
