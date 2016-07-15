<?php

namespace App\Http\Requests\Api\V1\Seller;

class ForgotPasswordRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'email' => 'required|email|exists:sellers',
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){
        return $this->rules;
    }

}