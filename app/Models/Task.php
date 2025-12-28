<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property int $id
 */
class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'status',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
