<?php

namespace App\Http\Controllers;
use App\Models;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

	public $inputs       = [];
	
	public $bladeContent = [];

	public function __construct()
	{

		\DB::connection()->enableQueryLog();
		$this->inputs = collect(request()->all());
	}
}