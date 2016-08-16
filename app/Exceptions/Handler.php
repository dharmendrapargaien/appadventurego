<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use League\OAuth2\Server\Exception as OAuth2Exception;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public $jsonResponse = [
        'status' => 'fail'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if($e instanceof TokenMismatchException)
            return $this->handleTokenMismatch();

        if($e instanceof HttpResponseException)
            return parent::render($request, $e);

        if($e instanceof ValidationException)
            return parent::render($request, $e);

        if ($e instanceof ModelNotFoundException) {
            return $this->setRender($request, $e, 404,  ['error' => 'Application not found']);
        }

        if($e instanceof NotFoundHttpException){
            return $this->setRender($request, $e, 404,  ['error' => 'Page not found']);
        }

        if($e instanceof OAuth2Exception\AccessDeniedException){ 
            return $this->setRender($request, $e, 500,  ['error' => 'The resource owner or authorization server denied the requestd.']);
        }

        if($e instanceof OAuth2Exception\InvalidRequestException){ 
            return $this->setRender($request, $e, 500,  ['error' => 'There is something missing.']);
        }

        if($e instanceof OAuth2Exception\InvalidRefreshException){ 
            return $this->setRender($request, $e, 500,  ['error' => 'Invalid refresh token.']);
        }

        if($e instanceof OAuth2Exception\UnsupportedGrantTypeException){ 
            return $this->setRender($request, $e, 500,  ['error' => 'Unsupported grant type.']);
        }

        if($e instanceof OAuth2Exception\InvalidClientException){ 
            return $this->setRender($request, $e, 500,  ['error' => 'Invalid client id/client secret provided.']);
        }

        return $this->setRender($request, $e);
    }

    /**
     * Function does error handling according to request type and enviornment
     * @param Request  $request 
     * @param Exception  $e       
     * @param integer $status  [status code]
     * @param array   $message [message array of json return]
     */
    public function setRender($request, $e, $status = 500, $message = ['error' => 'Server Error'])
    {
        //case when eviournment is not local
        //when request was ajax type
        if($request->ajax() || $request->wantsJson()){

            //getting the stacktrace when in local else returning the message intended
            $message = (env('APP_ENV') === 'local' || env('APP_ENV') === 'dev') ? $e->__toString() : $message;

            //if api request for websystem
            if($request->is('api/*')){

                $apiResponse = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);
                $apiResponse->setStatusCode($status);

                $errorResponse       = $apiResponse->withError($message, $status);
                $jsonContent         = $errorResponse->getData();
                $jsonContent->status = 'fail';
                
                $errorResponse->setData($jsonContent);

                return $errorResponse;
            }
            //else sending the default json response
            $this->jsonResponse['errors'] = $message;
            return response()->json($this->jsonResponse, $status);
        }
        //if env is local
        if(env('APP_ENV') === 'local' || env('APP_ENV') === 'dev'){
            return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);
        }

        $e = new HttpException($status, $e->getMessage());
        return $this->renderHttpException($e);
    }

    /**
     * Handle token mismatch exception
     * @return redirect 
     */
    private function handleTokenMismatch()
    {
        \AppHelper::setFlashMessage('error', 'Either your session has expired or cross site forgery token has expired.');
        return redirect()->back();
    }
}