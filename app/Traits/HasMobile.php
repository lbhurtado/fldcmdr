<?php

namespace App\Traits;

use App\Eloquent\Phone;

trait HasMobile
{
    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }
}
