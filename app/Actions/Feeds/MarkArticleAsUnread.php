<?php

namespace App\Actions\Feeds;

use StageRightLabs\Actions\Action;

/**
 * Mark an article as unread.
 *
 * Required:
 *  - Article
 */
class MarkArticleAsUnread extends Action
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
        $this->article->has_been_read = false;
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
