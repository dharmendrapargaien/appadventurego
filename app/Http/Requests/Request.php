<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use League\Fractal\Manager;
use Illuminate\Support\Facades\Input;
use EllipseSynergie\ApiResponse\Laravel\Response as ApiResponse;

abstract class Request extends FormRequest
{
    
    public $rules = [];

    /**
     * Json response data structure
     * @var array
     */
    public $jsonResponse = [
        'status' => 'fail'
    ];

    public function __construct()
    {

        parent::__construct();

        $manager = new Manager;
        $this->apiResponse = new ApiResponse($manager);

    }

    /**
     * set error response
     * @param  array  $errors
     * @return redirect/json response
     */
    public function response(array $errors)
    {
        
        if ($this->ajax() || $this->wantsJson()) {
            
            $errors              = collect($errors)->flatten()->toArray();
            $errorResponse       = $this->apiResponse->errorWrongArgs(array_values($errors));
            $jsonContent         = $errorResponse->getData();
            $jsonContent->status = 'fail';
            
            $errorResponse->setData($jsonContent);

            return $errorResponse;
        }

        return $this->redirector->to($this->getRedirectUrl())->withInput($this->except($this->dontFlash))->withErrors($errors, $this->errorBag);
    }

	abstract public function rules();

    /**
     * Function checks that the user is authorized to make this request
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Validate the input.
     *
     * @param  \Illuminate\Validation\Factory  $factory
     * @return \Illuminate\Validation\Validator
     */
    public function validator($factory)
    {
        return $factory->make(
            $this->sanitizeInput(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    /**
     * Sanitize the input.
     *
     * @return array
     */
    protected function sanitizeInput()
    {
        if (method_exists($this, 'sanitize'))
        {
            return $this->container->call([$this, 'sanitize']);
        }

        return $this->all();
    }
}