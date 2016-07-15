<?php

namespace App\Http\Requests\Api\V1\Seller;

class AdvertiseRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'seller_business_id' => 'required',
        'description'        => 'required|max:200',
        'start_date'         => 'required|date',
        'end_date'           => 'required|date|after:start_date',
        //'advertise'        => 'mimes:jpeg,jpg,png,bmp,gif'  
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){
        
        return $this->rules;
    }

}