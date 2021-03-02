<?php

namespace App\Actions\Feeds;

use App\Models\Article;
use StageRightLabs\Actions\Action;

/**
 * Restore an article that has been "soft" deleted.
 *
 * Required:
 *  - Article
 */
class RestoreArticle extends Action
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
        $this->article->restore();

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
