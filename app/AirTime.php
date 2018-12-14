<?php

namespace App;

use App\Eloquent\Phone;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchemalessAttributes;

class AirTime extends Model
{
    use HasSchemalessAttributes;

    protected $fillable = [
    	'mobile',
    ];

    public $casts = [
        'extra_attributes' => 'array',
    ];

    public function setMobileAttribute($value)
    {
    	$this->attributes['mobile'] = Phone::number($value);
    }

    public function getCampaignAttribute()
    {
        return $this->extra_attributes['campaign'] ?? null;
    }

    public function setCampaignAttribute($value)
    {
        $this->extra_attributes['campaign'] = $value;

        return $this;
    }
}
