<?php

namespace App\Models;

use App\Concerns\UuidAsPrimaryKey;
use App\Utilities\Arr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, UuidAsPrimaryKey;

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
     * Retrieve a value from the subscriptions 'extra' field.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getExtra($key = null, $default = null): mixed
    {
        if ($key) {
            return Arr::get($this->extra, $key, $default);
        }

        return $this->extra;
    }

    /**
     * Record a value in the subscription's 'extra' field.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setExtra($key, $value): void
    {
        $this->extra[$key] = $value;
    }
}
