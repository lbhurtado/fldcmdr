<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SMSWebHookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'from'  => 'origin mobile',
            'to'    => 'destination mobile',
        ];
    }

    public function rules()
    {
        return [
            'secret'    => [
                'required', 
                Rule::in([config('chatbot.webhook.sms.secret'), 'test'])
            ],
            'from'      => 'required|phone:PH,mobile',
            'to'        => 'required|phone:PH,mobile',
            'message'   => 'required|max:255'
        ];
    }
}
