<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaggablePivot extends Pivot
{
	use SoftDeletes;
}
