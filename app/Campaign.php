<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchemalessAttributes;

class Campaign extends Model
{
	use HasSchemalessAttributes;

    private $loadable = false;

    protected $fillable = [
    	'name',
    	'message',
        'extra_attributes',
    ];

    protected $casts = [
        'extra_attributes' => 'array',
    ];

    protected $appends = [
    	'air_time',
        'disabled'
    ];

    public function getAirTimeAttribute()
    {
        return $this->extra_attributes['air_time'];
    }

    public function getDisabledAttribute()
    {
        return $this->extra_attributes["disabled"] ?? false;
    }

    public function setDisabledAttribute($value)
    {
        $this->extra_attributes['disabled'] = $value;

        return $this;
    }

    public function setAirTimeAttribute($value)
    {
        $value = $this->extra_attributes['air_time'] = filter_var($value, 
        	FILTER_VALIDATE_FLOAT, 
        	array('flags' => FILTER_NULL_ON_FAILURE)
        );
        $this->save();
        $this->loadable = ($value > $this->getMinimumAirTimeTransfer());

        return $this;
    }

    protected function getMinimumAirTimeTransfer()
    {
        return 0;
    }

    public function isLoadable(): bool
    {
        return $this->loadable;
    }
}
