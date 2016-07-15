<?php

namespace App\Http\Controllers\Api\Seller\V1;

use File;
use Storage;
use Carbon\Carbon;
use App\Models\Seller;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\User\ProfileUpdateRequest;
use App\Http\Requests\Api\User\UserSettingRequest;

class SellerController extends BaseController
{
	/**
	 * Seller model instance
	 * @var App\Models Seller
	 */
	protected $sellerModel;

	
	public function __construct(Seller $seller)
	{
		parent::__construct();
		$this->sellerModel = $seller;
	}

	/**
	 * Updates seller profile
	 * @param  ProfileUpdateRequest $request
	 * @param  Seller $seller
	 * @return json
	 */
	public function profileUpdate(ProfileUpdateRequest $request, Seller $seller)
	{
		if ($request->hasFile('avatar')) {
			$seller = $this->imageUpload($request->file('avatar'), $seller);
		}

		$seller->update($request->only(['name', 'bio', 'phone']));

		return $this->response->withItem($seller, new UserTransformer);
	}

	
}