<?php

namespace Ptuchik\CoreUtilities\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Ptuchik\CoreUtilities\Constants\HttpStatusCode;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\Validator;

/**
 * Class Handler
 * @package Ptuchik\CoreUtilities\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     *
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception                $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        // If the request wants JSON (AJAX doesn't always want JSON)
        if ($request->wantsJson()) {

            // Convert some validations to suitable format
            if ($exception instanceof MethodNotAllowedHttpException || $exception instanceof ModelNotFoundException) {
                $exception = new NotFoundHttpException(trans(config('ptuchik-core-utilities.translations_prefix').'.not_found'));
            } elseif ($exception instanceof ValidationException) {
                return $this->parseValidationErrors($exception->validator);
            } elseif ($exception instanceof TokenMismatchException) {
                $exception = new Exception(trans(config('ptuchik-core-utilities.translations_prefix').'.csrf_token_error'));
            }

            // Parse and return response
            return $this->parseResponse($exception);
        }

        // Default to the parent class' implementation of handler
        return parent::render($request, $exception);
    }

    /**
     * Parse validation errors
     *
     * @param Validator $validator
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    protected function parseValidationErrors(Validator $validator)
    {
        // Get error messages
        $validatorErrors = $validator->getMessageBag()->getMessages();

        // Create an empty errors container array
        $errors = [];

        // Loop through each error and add to container
        if (is_array($validatorErrors)) {
            foreach ($validatorErrors as $key => $message) {
                $errors[][$key] = $message[0];
            }
        }

        $response = [
            'errors'  => $errors,
            'message' => 'Unprocessable entity',
            'code'    => HttpStatusCode::UNPROCESSABLE_ENTITY
        ];

        // Return error response
        return response()->json($response, HttpStatusCode::UNPROCESSABLE_ENTITY);
    }

    /**
     * Parse exception and response with error message
     *
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function parseResponse(Exception $exception)
    {
        // Define the response
        $response = [
            'errors' => [
                [
                    'general' => $exception->getMessage() ?? trans(config('ptuchik-core-utilities.translations_prefix').'.something_went_wrong')
                ]
            ]
        ];

        // If the app is in debug mode
        if (config('app.debug')) {

            // Add the exception class name, message and stack trace to response
            $response['exception'] = get_class($exception);
            $response['trace'] = $exception->getTrace();
        }

        $response['message'] = $exception->getMessage();
        $response['code'] = $this->getStatusCode($exception);

        // Return a JSON response with the response array and status code
        return response()->json($response, $response['code']);
    }

    /**
     * Get status code from exception
     *
     * @param \Exception $exception
     *
     * @return int
     */
    protected function getStatusCode(Exception $exception)
    {
        // If this exception is an instance of HttpException
        if ($this->isHttpException($exception)) {

            // Grab the HTTP status code from the Exception
            return $exception->getStatusCode();
        }

        // Otherwise return 400
        return HttpStatusCode::BAD_REQUEST;
    }
}
