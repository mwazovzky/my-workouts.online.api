<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function actions(): MorphMany
    {
        return $this->morphMany(Action::class, 'actionable')->orderBy('order');
    }
}
