<?php declare(strict_types=1);

namespace App\Application\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /** {@inheritDoc} */
    protected $dontReport = [];

    /** {@inheritDoc} */
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    /**
     * Report or log an exception.
     *
     * @throws Throwable
     */
    public function report(Throwable $exception): void
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
        });
    }
}
