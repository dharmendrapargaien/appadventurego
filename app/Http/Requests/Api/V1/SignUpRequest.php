<?php

namespace App\Http\Requests\Api\V1\Seller;

class SignUpRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'name'       => 'required|max:50',
        'email'      => 'required|email|max:255|unique:sellers,email',
        'password'   => 'required|min:6|confirmed',
        'phone_no'   => 'required|unique:sellers,phone_no',
        'business'   => 'required|array',
        'gender'     => 'required',
    ];

    public $messages  = [];
    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){
        
        return $this->rules;
    }

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function messages(){

        $this->messages['business.array'] = 'Please select business.';

        return $this->messages;
    }
}