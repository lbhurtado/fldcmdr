<?php

namespace App;

use App\Tag;
use App\Eloquent\Phone;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSchemalessAttributes;

class AirTime extends Model
{
    use HasSchemalessAttributes;

    protected $fillable = [
    	'name',
        'amount'
    ];

    public $casts = [
        'extra_attributes' => 'array',
    ];

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
