<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        $statusCode = 500;

        $error = [
            'success' => false,
            'message' => 'Erro ao executar esta operação, entre em contato com o administrador do sistema',
            //'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ValidationException) {
            $error['message'] = $exception->validator->getMessageBag()->first();
            $statusCode = 422;

            return response()->json([
                'message' => $error['message'],
            ], $statusCode);
        } elseif ($exception instanceof AuthenticationException) {
            $error['message'] = 'Não autenticado';
            $error['reconnect'] = true;
            $statusCode = 401;
        }

        if ($this->isHttpException($exception)) {
            $statusCode = $exception->getStatusCode();
            $error['message'] = $exception->getMessage();

            if ($statusCode == 404) {
                return response()->view('errors.404', [], 404);
            }
        }

	return response()->json($error, $statusCode);
        //return parent::render($request, $exception);
    }
}

