<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Handler $exception
     * @return void
     */
    public function report(\Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $exception)
    {

//        // If the request wants JSON (AJAX doesn't always want JSON)
//        if ($request->wantsJson()) {
//            // Define the response
//            $response = [
//                'errors' => 'Sorry, something went wrong.',
//            ];
//
//            if (config('app.debug')) {
//                $response['exception'] = get_class($exception); // Reflection might be better here
//                $response['message'] = $exception->getMessage();
//                $response['trace'] = $exception->getTrace();
//            }
//
//            $status = 400;
//            if ($this->isHttpException($exception)) {
//                $status = $exception->getStatusCode();
//            }
//
//            // Return a JSON response with the response array and status code
//            return response()->json($response, $status);
//        }

        return parent::render($request, $exception);
    }
}
