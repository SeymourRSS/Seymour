<?php

namespace App\Models;

use App\Concerns\HasExtras;
use App\Concerns\UuidAsPrimaryKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasExtras, HasFactory, SoftDeletes, UuidAsPrimaryKey;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
        'feed_timestamp' => 'datetime',
    ];

    /**
     * The user who owns this subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
