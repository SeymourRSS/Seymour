<?php

namespace App\Actions\Feeds;

use App\Models\Article;
use StageRightLabs\Actions\Action;

/**
 * Mark an article as having been read.
 *
 * Required:
 *  - Article
 */
class MarkArticleAsRead extends Action
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
        $this->article->has_been_read = true;
        $this->article->save();

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
