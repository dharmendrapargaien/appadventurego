<?php

namespace App\Http\Requests\Api\V1;

class NearestMarkerRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'lat' => 'required',
        'lon' => 'required',
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){
    
        return $this->rules;
    }
}