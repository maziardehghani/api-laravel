<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof QueryException)
        {
            DB::rollBack();
            return $this->ErrorResponse($e->getMessage() , 404 );
        }

        if ($e instanceof \ErrorException)
        {
            DB::rollBack();
            return $this->ErrorResponse($e->getMessage() , 404 , [
                'exception_details' => [
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile()
                ]
            ]);
        }
        DB::rollBack();

    }
}
