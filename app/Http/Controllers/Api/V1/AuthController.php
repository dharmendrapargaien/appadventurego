<?php

namespace App\Http\Controllers\Api\V1;

use Mail;

use App\Http\Requests\Api\V1\SignUpRequest;
use App\Http\Requests\Api\V1\AuthenticationRequest;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\ActiveAcountRequest;

use Authorizer, Auth;

use League\Fractal;

class AuthController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Checks user credentials and provide them with an authorization token for subsequent requests
	 * @param  \App\Http\Requests\Api\V1\AuthenticationRequest $request
	 * @return json
	 */
	public function authenticate(AuthenticationRequest $request)
	{
		//request for oauth authorization
		$authorizer            = Authorizer::issueAccessToken();
		$user                  = $this->userModel->whereEmail($this->inputs['email'])->whereStatus(1)->first();
		
		//add user data 
		$authorizer['id']      = $user->id;
		$authorizer['email']   = $user->email;
		$authorizer['name']    = $user->name;
		$authorizer['role_id'] = $user->role_id;

		return response()->json([
			'status' => 'success',
			'data'   => $authorizer
		], 200);
    }

	/**
	 * Sends a temporary password to user
	 * @param  ForgotPasswordRequest $request
	 * @return json
	 */
	public function forgotPassword(ForgotPasswordRequest $request)
	{
		\DB::beginTransaction();

		$email = $request->email;
		$user  = $this->userModel->whereEmail($email)->first();

		$this->sendTemporaryPassword($user);

		\DB::commit();
	
		$data = [];
        return response()->json([
			'status' => 'success',
			'data'   => $data], 
		200);
	}

	/**
	 * Generates a temporary password and send and email
	 * @param  App\Models\user $user
	 * @return boolean
	 */
	public function sendTemporaryPassword($user)
	{
		$chars    = "abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789!@#&";
		$password = substr( str_shuffle( $chars ), 0, 6 );

		$user->temporary_password = bcrypt($password);
		$user->save();
		$user->temporary_password = $password;
    	return Mail::send('auth.emails.api.v1.temporary_password', ['user' => $user], function ($m) use ($user) {
    		$m->to($user->email, $user->name)->subject('Test Api Password');
    	});
	}

	/**
	 * Signup a new user
	 * @param  \App\Http\Requests\Api\V1\SignUpRequest $request
	 * @return json
	 */
	public function signup(SignUpRequest $request)
	{
		\DB::beginTransaction();

		//create new user
		$user = $this->userModel->create([
			'email'    => trim($request->email),
			'password' => bcrypt(trim($request->password)),
			'name'     => trim($request->get('name')),
			'status'   => 1,
			'role_id'  => 2,
		]);
		
		//authonticate new user
		$authorizer            = Authorizer::issueAccessToken();

		//add user data 
		$authorizer['id']      = $user->id;
		$authorizer['email']   = $user->email;
		$authorizer['name']    = $user->name;
		$authorizer['role_id'] = $user->role_id;

		//$this->sendActivationCode($user);
		\DB::commit();
		
		return response()->json([
			'status' => 'success',
			'data'   => $authorizer
		], 200);
	}

	/**
	 * Create and send activation code to newly registered user
	 * @param  \App\Models\user $user
	 * @return boolean
	 */
	private function sendActivationCode($user)
	{

		$confirmation_code         = mt_rand(100000, 999999);
		$user->confirmation_code   = $confirmation_code;
		$user->save();

		// Send an email about this
		return Mail::send('auth.emails.api.v1.activation', ['user' => $user], function ($m) use ($user) {
			$m->to($user->email, $user->name)->subject('app activation code');
		});
	}

	/**
	 * Activate user with confirmation code
	 * @param  int $confirmation_code [Confirmation code which user got in email]
	 * @return json
	 */
	public function activate(ActiveAcountRequest $request)
	{
		
		$user =  $this->userModel->getuserConfirmationData($request->input('email'), $request->input('confirmation_code'));
		
		$user->status            = 1;
		$user->confirmation_code = null;
		$user->save();

		//request for oauth authorization
		$authorizer = Authorizer::issueAccessToken();
		$user       = $this->userModel->getuserData($request->input('email'));
		
		//add user data 
		$authorizer['id']    = $user->id;
		$authorizer['email'] = $user->email;
		$authorizer['name']  = $user->name;
		
		return response()->json([
			'status' => 'success',
			'data'   => $authorizer
		], 200);
	}
}