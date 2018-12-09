<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Survey extends Model
{
	protected $fillable = [
		'started_at',
		'ended_at',
	];

    protected $dates = [
        'started_at',
        'ended_at',
        'created_at',
        'updated_at',
    ];
    
    public function user():BelongsTo
    {
    	return $this->belongsTo(User::class);
    }

    public function askable():MorphTo
    {
        return $this->morphTo();
    }
}
