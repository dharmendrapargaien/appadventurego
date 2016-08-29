<?php

namespace App\Http\Requests\Api\V1;

class EventMarkerFlagRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'marker_id'   => 'required',
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules()
    {
   
        return $this->rules;
    }
}