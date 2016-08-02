<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::macro('after', function ($callback) {
	
   $this->events->listen('router.filter:after:newrelic-patch', $callback);
});

Route::get('/', 'Auth\AuthController@getLogin');

Route::group(['middleware' => ['auth']], function(){

	Route::resource('users','UserController', ['except' => ['show','destroy']]);
});