<?php

namespace App\Actions\Feeds;

use App\Models\Article;
use App\Utilities\Str;
use StageRightLabs\Actions\Action;

/**
 * Create a new article from a feed entry and store it in the database.
 *
 * Required:
 *  - Entry
 *  - Subscription
 */
class RecordArticle extends Action
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
        // Convert the entry timestamp to UTC if present.
        $timestamp = $input['entry']->getTimestamp();
        if ($timestamp) {
            $timestamp->timezone('UTC')->format('Y-m-d H:i:s');
        }

        // Create a new article.
        $this->article = Article::create([
            'content' => $input['entry']->getContent(),
            'entry_timestamp' => $timestamp,
            'identifier' => $input['entry']->getIdentifier(),
            'link_to_source' => $input['entry']->getLinkToSource(),
            'rights' => $input['entry']->getRights(),
            'slug' => Str::slug($input['entry']->getTitle()),
            'subscription_uuid' => $input['subscription']->uuid,
            'summary' => $input['entry']->getSummary(),
            'title' => $input['entry']->getTitle(),
        ]);

        if ($input['entry']->getAuthors()->isNotEmpty()) {
            $this->article->setExtra('authors', $input['entry']->getAuthors()->toArray());
        }

        if ($links = $input['entry']->getExtra('links')) {
            $this->article->setExtra('links', $links->toArray());
        }

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
            'entry', // Entry
            'subscription', // Subscription
        ];
    }
}
