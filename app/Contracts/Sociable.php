<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Sociable
{
    public function groups(): MorphToMany;

    public function roles(): MorphToMany;
}