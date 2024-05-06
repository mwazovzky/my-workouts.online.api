<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'sets_number',
        'repetitions',
        'exercise_id',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function sets(): HasMany
    {
        return $this->hasMany(Set::class);
    }
}
