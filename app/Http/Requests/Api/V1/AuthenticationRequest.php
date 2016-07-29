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
        'client_secret' => 'required|exists:oauth_clients,secret',
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){

        return $this->rules;
    }

    /**
     * Sanitize input before validation.
     *
     * @return array
     */
    public function sanitize()
    {
        if ($this->request->has('email')) {

            $input          = $this->all();
            $input['email'] = strtolower($input['email']);
            $this->replace($input);
        }
        
        return $this->all();
    }
}