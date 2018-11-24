<?php

namespace App;

use App\{User, Question};
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Answer as AnswerContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model implements AnswerContract
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    protected $fillable = [
    	'answer', 'askable_id', 'askable_type', 'weight',
    ];
    
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'answer' => 'array',
        'weight' => 'integer',
    ];

    public function getAnswerAttribute($value)
    {
        return explode(",", $value);
    }

    public function setAnswerAttribute($value)
    {
        $this->attributes['answer'] = implode($value, ",");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function askable():MorphTo
    {
        return $this->morphTo();
    }
}
