<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Set extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'weight',
        'repetitions',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'bool',
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }
}
