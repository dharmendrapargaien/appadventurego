<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{

	public $inputs = [];

	public function __construct()
	{
		$this->inputs = collect(request()->all());
		parent::__construct();
	}
}