<?php

namespace App\Http\Controllers\Api\Seller\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Business;

class BaseController extends Controller
{

	public $inputs = [];

	public function __construct()
	{
		$this->inputs = collect(request()->all());
		parent::__construct();
	}

	/**
	 * fetch business data
	 * 
	 * @return json
	 */
	public function businessLists()
	{
		
		try{
			
			return response()->json([
				'status' => 'success',
				'data'   => Business::select('id','title')->whereStatus(1)->lists('title', 'id')
			],200);
		} catch(Execption $e){

			return response()->json(['error' => 'Server error'], 501);
		}
	}
}