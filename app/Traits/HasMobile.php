<?php

namespace App\Traits;

use App\Eloquent\Phone;

trait HasMobile
{
    public static function findByMobile($mobile)
    {
    	return static::withMobile($mobile)->first();
    }

	public function setMobile($mobile)
	{
		$this->mobile = $mobile;
		$this->save();

		return $this;
	}

    public function getMobileAttribute()
    {
        //improve on this
        //make this faster
        return remove_non_ascii_for_smsc_consumption($this->attributes['mobile']);
    }

    public function setMobileAttribute($value)
    {
        $this->attributes['mobile'] = Phone::number($value);
    }

    public function scopeWithMobile($query, $value)
    {
        return $query->where('mobile', Phone::number($value));
    }
}
