<?php

namespace App;

use App\Question;
use App\Traits\HasSchemalessAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Contracts\Category as CategoryContract;

class Category extends Model implements CategoryContract
{
    use SoftDeletes, HasSchemalessAttributes;

    public $guarded = ['id'];

    public $casts = [
        'extra_attributes' => 'array',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 'enabled_at',
    ];

    protected $appends = [
        'completion', 'completed', 'enabled'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function getCompletionAttribute(): float
    {
        $questions = $this->questions->count();
        $answered = $this->questions()->answered()->count();

        return ($answered / $questions) * 100;
    }

    public function getCompletedAttribute(): bool
    {
        return $this->questions()->answered == $this->questions;
    }

    public function getTwosomeAttribute(): bool
    {
        return $this->extra_attributes["twosome"] ?? false;
    }

    public function getRewardAttribute(): float
    {
        return $this->extra_attributes["reward"] ?? 0.00;
    }

    public function getPollCountAttribute(): bool
    {
        return $this->extra_attributes["pollcount"] ?? false;
    }

    public function getEnabledAttribute(): bool
    {
        return $this->enabled_at && $this->enabled_at <= now();
    }
}

