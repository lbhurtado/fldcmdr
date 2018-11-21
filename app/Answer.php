<?php

namespace App;

use MCesar\Survey\Models\Answer as BaseAnswer;

class Answer extends BaseAnswer implements \MCesar\Survey\Contracts\Answer
{
    protected $fillable = [
    	'answer',
    ];
}
