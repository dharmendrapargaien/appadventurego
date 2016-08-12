<?php

namespace App\Http\Requests\Api\V1;

class CreateMarkerRequest extends \App\Http\Requests\Request
{
    /**
     * Basic rules array
     * @var array
     */
    public $rules = [
        'marker_type'   => 'required|max:100|exists:marker_types,type_slug',
        'name'          => 'required|max:100',
        'description'   => 'required|max:200',
        'lat'           => 'required',
        'lon'           => 'required',
        'marker_points' => 'required',
        'marker_stars'  => 'required',
    ];

    /**
     * Function to create rules dynamically
     * @return array [rules array]
     */
    public function rules(){
        
        //if marker type is event then we need data and time
        if($this->request->has('marker_type') && $this->request->get('marker_type') == 'event') {
            
            $this->rules['marker_date']    = 'required|date|after:today';
            $this->rules['marker_time']    = 'required';    
        }

        return $this->rules;
    }

    /**
     * Sanitize input before validation.
     *
     * @return array
     */
    public function sanitize()
    {
        if ($this->request->has('name')) {

            $input          = $this->all();
            $input['name']  = strtolower($input['name']);
            $this->replace($input);
        }
        return $this->all();
    }
}