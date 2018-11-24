<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollCount extends Model
{
    protected $casts = [
        'sum' => 'integer',
    ];

    public static function result()
    {
    	$pollcount = static::with('question')->get();
    	
    	return $pollcount->sortByDesc('sum')->keyBy('question.code')->map(function($item) {
    		return $item->sum;
    	});
    }

    public function category()
    {
    	return $this->belongsTo(Category::class);
    }

    public function question()
    {
    	return $this->belongsTo(Question::class);
    }
}
