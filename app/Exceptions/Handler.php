<?php

namespace App\Exceptions;

use Throwable;
use App\Helpers\Api;
use ReflectionClass;
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (preg_match('/(.*)\/api\/(.*)/i', $request->fullUrl())) { // API request.
            $code = $exception->getCode() | 404;
            $className = (new ReflectionClass($exception))->getShortName();

            switch ($className) {
                case 'ModelNotFoundException':
                    $response = Api::generateErrorResponse(404, 'NotFoundError', 'The resource you were looking for was not found.');
                    break;

                default:
                    $response = Api::generateErrorResponse($code, $className, $exception->getMessage());
                    break;
            }

            return $response;
        }

        return parent::render($request, $exception);
    }
}
