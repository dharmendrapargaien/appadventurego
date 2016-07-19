<?php

namespace App\Http\Requests\Api\V1;

class SignUpRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'name'       => 'required|max:50',
        'email'      => 'required|email|max:255|unique:users,email',
        'password'   => 'required|min:6|confirmed',
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){
        
        return $this->rules;
    }
}