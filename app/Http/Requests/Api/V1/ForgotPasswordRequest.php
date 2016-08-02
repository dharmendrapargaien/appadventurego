<?php

namespace App\Http\Requests\Api\V1;

class ForgotPasswordRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'email' => 'required|email|exists:users',
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){
        return $this->rules;
    }

}