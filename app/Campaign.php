<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchemalessAttributes;

class Campaign extends Model
{
	use HasSchemalessAttributes;

    protected $fillable = [
    	'name',
    	'message',
    ];

    protected $casts = [
        'extra_attributes' => 'array',
    ];

    protected $appends = [
    	'air_time',
    ];

    public function getAirTimeAttribute()
    {
        return $this->extra_attributes['air_time'];
    }

    public function setAirTimeAttribute($value)
    {
        $this->extra_attributes['air_time'] = filter_var($value, 
        	FILTER_VALIDATE_FLOAT, 
        	array('flags' => FILTER_NULL_ON_FAILURE)
        );
        $this->save();

        return $this;
    }
}
