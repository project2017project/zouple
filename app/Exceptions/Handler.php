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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson() === false && $this->isFriendlyUploadException($exception)) {
            return redirect()->back()->withInput()->with('alert-danger', $exception->getMessage());
        }

        return parent::render($request, $exception);
    }

    private function isFriendlyUploadException(Exception $exception)
    {
        if (!$exception instanceof \RuntimeException && !$exception instanceof \InvalidArgumentException) {
            return false;
        }

        $message = $exception->getMessage();

        return strpos($message, 'Local media upload failed') !== false
            || strpos($message, 'Cloudinary') !== false
            || strpos($message, 'New uploads are not saved locally') !== false
            || strpos($message, 'Please upload a valid') !== false;
    }
}
