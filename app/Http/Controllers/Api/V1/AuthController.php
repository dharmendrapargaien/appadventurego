<?php

namespace App\Http\Controllers\Api\Seller\V1;

use Mail;
use App\Models\Seller;

use App\Transformers\Api\V1\Seller\AuthenticateTransformer;
use App\Transformers\Api\V1\Seller\SellerTransformer;

use App\Http\Requests\Api\V1\Seller\SignUpRequest;
use App\Http\Requests\Api\V1\Seller\AuthenticationRequest;
use App\Http\Requests\Api\V1\Seller\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Seller\ActiveAcountRequest;

use Authorizer, Auth;

use League\Fractal;

class AuthController extends BaseController
{
	/**
	 * Seller model instance
	 * @var App\Models\Seller
	 */
	protected $sellerModel;

	public function __construct(Seller $seller)
	{
		parent::__construct();
		$this->sellerModel = $seller;
	}

	/**
	 * Checks seller credentials and provide them with an authorization token for subsequent requests
	 * @param  \App\Http\Requests\Api\V1\Seller\AuthenticationRequest $request
	 * @return json
	 */
	public function authenticate(AuthenticationRequest $request)
	{

		//request for oauth authorization
		$authorizer = Authorizer::issueAccessToken();
		$seller     = $this->sellerModel->getSellerData($request->input('email'));
		
		//add seller data 
		$authorizer['id']    = $seller['id'];
		$authorizer['email'] = $seller['email'];
		$authorizer['name']  = $seller['name'];

		// We have an access token. Now we need to return that.
        $resource = new \App\Fractal\Item($authorizer, new AuthenticateTransformer);
        
        return response()->json($resource->getSuccess(), 200);
    }

	/**
	 * Sends a temporary password to seller
	 * @param  ForgotPasswordRequest $request
	 * @return json
	 */
	public function forgotPassword(ForgotPasswordRequest $request)
	{
		\DB::beginTransaction();

		$email  = $request->email;
		$seller = $this->sellerModel->whereEmail($email)->first();

		$this->sendTemporaryPassword($seller);

		\DB::commit();
	
		// We have an API key. Now we need to return that.
        $resource = new \App\Fractal\Item($seller, new SellerTransformer);
        return response()->json($resource->getSuccess(), 200);
	}

	/**
	 * Generates a temporary password and send and email
	 * @param  App\Models\Seller $seller
	 * @return boolean
	 */
	public function sendTemporaryPassword($seller)
	{
		$chars    = "abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789!@#&";
		$password = substr( str_shuffle( $chars ), 0, 6 );

		$seller->temporary_password = bcrypt($password);
		$seller->save();
		$seller->temporary_password = $password;
    	return Mail::send('auth.emails.api.v1.seller.temporary_password', ['seller' => $seller], function ($m) use ($seller) {
    		$m->to($seller->email, $seller->name)->subject('Test Api Password');
    	});
	}

	/**
	 * Signup a new seller
	 * @param  \App\Http\Requests\Api\V1\Seller\SignUpRequest $request
	 * @return json
	 */
	public function signup(SignUpRequest $request)
	{
		\DB::beginTransaction();

		$request_data = [
			'email'    => trim($request->email),
			'password' => bcrypt(trim($request->password)),
			'phone_no' => trim($request->phone_no),
			'name'     => trim($request->get('name')),
			'gender'   => $request->get('gender'),
		];
		
		$seller = $this->sellerModel->create($request_data);
		//store seller business types
		$seller->businesses()->attach($request->get('business'), ["business_order" => 1, 'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()]);
		
		$this->sendActivationCode($seller);

		\DB::commit();

		// We have an API key. Now we need to return that.
        $resource = new \App\Fractal\Item($seller, new SellerTransformer);
        return response()->json($resource->getSuccess(), 200);
	}

	/**
	 * Create and send activation code to newly registered seller
	 * @param  \App\Models\Seller $seller
	 * @return boolean
	 */
	private function sendActivationCode($seller)
	{

		$confirmation_code         = mt_rand(100000, 999999);
		$seller->confirmation_code = $confirmation_code;
		$seller->save();

		// Send an email about this
		return Mail::send('auth.emails.api.v1.seller.activation', ['seller' => $seller], function ($m) use ($seller) {
			$m->to($seller->email, $seller->name)->subject('app activation code');
		});
	}

	/**
	 * Activate seller with confirmation code
	 * @param  int $confirmation_code [Confirmation code which seller got in email]
	 * @return json
	 */
	public function activate(ActiveAcountRequest $request)
	{
		
		$seller =  $this->sellerModel->getSellerConfirmationData($request->input('email'), $request->input('confirmation_code'));
		
		$seller->status            = 1;
		$seller->confirmation_code = null;
		$seller->save();

		//request for oauth authorization
		$authorizer = Authorizer::issueAccessToken();
		$seller     = $this->sellerModel->getSellerData($request->input('email'));
		
		//add seller data 
		$authorizer['id']    = $seller->id;
		$authorizer['email'] = $seller->email;
		$authorizer['name']  = $seller->name;

		// We have an access token. Now we need to return that.
        $resource = new \App\Fractal\Item($authorizer, new AuthenticateTransformer);
        
        return response()->json($resource->getSuccess(), 200);
	}
}