<?php

namespace App\Http\Requests\Api\V1;

class AuthenticationRequest extends \App\Http\Requests\Request
{ 
    /**
     * Basic rules array
     * @var array
     */
    
    public $rules = [
        'email'         => 'required|email|exists:users,email',
        'password'      => 'required',
        'grant_type'    => 'required',
        'client_id'     => 'required|exists:oauth_clients,id',
        'client_secret' => 'required|exists:oauth_clients,secret'
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){

        return $this->rules;
    }
}