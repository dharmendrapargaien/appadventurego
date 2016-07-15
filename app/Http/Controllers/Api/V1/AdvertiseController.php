<?php

namespace App\Http\Controllers\Api\Seller\V1;

use App\Models;
use League\Fractal;
use FileStorage;

use App\Transformers\Api\V1\Seller\AdvertiseListTransformer;
use App\Http\Requests\Api\V1\Seller\AdvertiseRequest;

class AdvertiseController extends BaseController
{

	/**
	 * Seller model instance
	 * @var App\Models\Seller
	 */
	protected $sellerBusinessAdvertiseModel;

	public function __construct(Models\SellerBusinessAdvertise $sellerBusinessAdvertise)
	{
		parent::__construct();
		$this->sellerBusinessAdvertiseModel = $sellerBusinessAdvertise;
	}

	/**
	 * fetch list of all seller business advetise list
	 * @param  Models\Seller $seller
	 * @return json
	 */
	public function index(Models\Seller $seller)
	{
		
		//request for perticular business
		if ($this->inputs->has('business_id') && !empty($this->inputs->has('business_id')) ) {

			$seller_business_id = Models\SellerBusiness::whereSellerId($seller->id)->whereBusinessId($this->inputs('business_id'))->first()['id'];
		} else {

			$seller_business_id = Models\SellerBusiness::whereSellerId($seller->id)->whereBusinessOrder(1)->first()['id'];
		}


		// We have an access token. Now we need to return that.
        $resource = new \App\Fractal\Item($this->sellerBusinessAdvertiseModel->getAdverise($seller_business_id), new AdvertiseListTransformer);
        
        return response()->json($resource->getSuccess(), 200);
	}

	/**
	 * store seller advertise
	 * @return json
	 */
	public function store(AdvertiseRequest $request)
	{

		\DB::beginTransaction();
		
		$request_data = [
			'description'        => trim($this->inputs['description']),
			'start_date'         => trim($this->inputs['start_date']),
			'end_date'           => trim($this->inputs['end_date']),
			'seller_business_id' => $this->inputs['seller_business_id'],
		];

		$advertise = $this->sellerBusinessAdvertiseModel->create($request_data);
		
		if($this->inputs->has('advertise_images')){
			
			$advertise = $this->uploadAllImages($this->inputs['advertise_images'], $advertise);
		} 
		
		\DB::commit();

		// We have an API key. Now we need to return that.
        $resource = new \App\Fractal\Item($this->sellerBusinessAdvertiseModel->getAdverise($this->inputs['seller_business_id']), new AdvertiseListTransformer);
        
        return response()->json($resource->getSuccess(), 200);
	}

	/**
	 * upload all images
	 * @param  array  $advertise_images
	 * @param  collection $advertise
	 * @return void
	 */
	private function uploadAllImages($advertise_images = [], $advertise)
	{	
		
		foreach ($advertise_images as $key => $image) {
			
			if(!$image)
				continue;
			$data = new \App\Models\SellerAdvertiseImage;
			$data->seller_business_advertise_id = $advertise->id;
			$data->name  = $this->imageUpload($image, $advertise);
			$data->url = url('uploads/advertise' . $data->name);

			if($key == 0)
				$data->featured_image = 1;	

			$data->save();
		}
	}

	/**
	 * upload individual image
	 * @param  array $file
	 * @param  collection $advertise
	 * @return string
	 */
	private function imageUpload($file, $advertise)
	{
		
		// Create a unique file name
		$timestamp = str_replace([' ', ':'], '-',  \Carbon\Carbon::now()->toDateTimeString());
		$name      = $timestamp. '-' .$file->getClientOriginalName();
		
		$file->move(public_path() . '/uploads/advertise/', $name);
		
		return $name;
	}
}
