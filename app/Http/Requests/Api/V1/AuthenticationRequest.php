<?php

namespace App\Http\Requests\Api\V1;

class AuthenticationRequest extends \App\Http\Requests\Request
{ 
    /**
     * Basic rules array
     * @var array
     */
    
    public $rules = [
        'password'      => 'required',
        'client_id'     => 'required|exists:oauth_clients,id',
        'client_secret' => 'required|exists:oauth_clients,secret'
    ];

    public $messages = [];
    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){

        if (request()->has('email') && (is_numeric(request()->get('email')))) {

            $this->rules['email'] = 'required|exists:users,phone_no';
        } else {
            $this->rules['email'] = 'required|email|exists:users,email';
        }
        
        return $this->rules;
    }

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function messages(){

        $this->messages['email.required'] = 'The email or phone no field is required.';

        if (request()->has('email') && (is_numeric(request()->get('email')))) {

            $this->messages['email.exists'] = 'The selected phone no is invalid.';
        } 
        return $this->messages;
    }
}