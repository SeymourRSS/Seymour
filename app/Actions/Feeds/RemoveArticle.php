<?php

namespace App\Actions\Feeds;

use App\Models\Article;
use StageRightLabs\Actions\Action;

/**
 * Apply a 'deleted_at' timestamp to an article to "soft" delete it.
 *
 * Required:
 *  - Article
 */
class RemoveArticle extends Action
{
    /**
     * @var Article
     */
    public $article;

    /**
     * Handle the action.
     *
     * @param Action|array $input
     * @return self
     */
    public function handle($input = [])
    {
        $this->article = $input['article'];
        $this->article->delete();

        return $this->complete();
    }

    /**
     * The input keys required by this action.
     *
     * @return array
     */
    public function required()
    {
        return [
            'article', // Article
        ];
    }
}
