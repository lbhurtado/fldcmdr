<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchemalessAttributes;

class Tag extends Model
{
	use HasSchemalessAttributes;

    protected $fillable = [
    	'code',
    	'description',
    ];

    public $casts = [
        'extra_attributes' => 'array',
    ];

    public function taggable()
    {
        return $this->morphTo();
    }
}
