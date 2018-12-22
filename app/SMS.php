<?php

namespace App;

use App\Eloquent\Phone;
use App\Jobs\InviteStubHolder;
use ShrooPHP\Pattern\Interpreter;
use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{    
    protected $fillable = [
        'from',
        'to',
        'message',
    ];

    public function match($pattern, $callback)
    {
        return optional($this->interpret($pattern), function ($interpretation) use ($callback) {
            $arguments = array_flatten($interpretation);             
            if (is_callable($callback))
                return $callback(...$arguments);
            else {
                extract($this->parse($callback));

                return app($class)->$method(...$arguments);
            }
        });
    }

    protected function interpret($pattern)
    {
        return (new Interpreter)->interpret($pattern)->match($this->message);
    }

    protected function parse($callback)
    {
        return (new Interpreter)->interpret('{class}@{method}')->match($callback);
    }

    public function checkStubAndInvite()
    {
        InviteStubHolder::dispatch($this);
    }

    public function setFromAttribute($value)
    {
        $this->attributes['from'] = Phone::number($value);
    }

    public function setToAttribute($value)
    {
        $this->attributes['to'] = Phone::number($value);
    }
}
