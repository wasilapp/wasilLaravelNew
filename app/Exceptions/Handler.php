<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use App\Exceptions\CustomValidationException;
use App\Http\Trait\MessageTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    use MessageTrait;
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
        if ($exception instanceof CustomValidationException) {
            return response()->json($exception->getMessage(), 422);
        }

        return parent::render($request, $exception);
    }


    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($request->expectsJson()){
            return response()->json(['error'=>'Unauthenticated'],401);
        }


        $guard = Arr::get ($exception->guards(),0);

        switch ($guard){
            case 'admin':
                $login = 'admin.login';
                break;
            case 'manager':
                $login = 'manager.login';
                break;
            default:
                $login='user.login';
                break;
        }

        return redirect()->guest(route($login));
    }

    public function handleException(Throwable $e)
    {
        if ($e instanceof HttpException) {
            $code = $e->getStatusCode();
            $defaultMessage = \Symfony\Component\HttpFoundation\Response::$statusTexts[$code];
            $message = $e->getMessage() == "" ? $defaultMessage : $e->getMessage();
            return $this->errorResponse($message, $code);
        } else if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));
            return $this->errorResponse("Does not exist any instance of {$model} with the given id", Response::HTTP_NOT_FOUND);
        } else if ($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_FORBIDDEN);
        } else if ($e instanceof TokenBlacklistedException) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } else if ($e instanceof AuthenticationException) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } else if ($e instanceof ValidationException) {
            $errors = $e->validator->errors()->getMessages();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else if ($e instanceof RouteNotFoundException) {
            $errors = $e->getMessage();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else if ($e instanceof TokenInvalidException) {
            $errors = $e->getMessage();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else if ($e instanceof TokenExpiredException) {
            $errors = $e->getMessage();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else if ($e instanceof JWTException) {
            $errors = $e->getMessage();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            if (config('app.debug'))
                return $this->dataResponse($e->getMessage());
            else {
                return $this->errorResponse('Try later', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

}

