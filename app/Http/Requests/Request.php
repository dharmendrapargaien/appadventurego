<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    
    public $rules = [];

	abstract public function rules();

	/**
	 * Function checks that the user is authorized to make this request
	 * @return boolean
	 */
    public function authorize()
    {
        return true;
    }
}
