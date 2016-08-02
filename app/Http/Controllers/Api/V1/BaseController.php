<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models;

class BaseController extends Controller
{

	public $inputs = [];

	public $userModel;
	
	public function __construct()
	{
		
		parent::__construct();

		$this->inputs    = collect(request()->all());
		$this->userModel = new Models\User;
	}
}