<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CustomThrottle;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'custom.throttle' => CustomThrottle::class,
        ]);

        //The rate limiting parameters in your bootstrap/app.php (custom.throttle:50,1) are now overridden by CustomThrottle dynamic logic, so you don't need to change anything else in your configuration.
        $middleware->api(append: [
            'custom.throttle:50,1'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'true',
                    'code' => '03v' . 422,
                    'message' => 'اعتبارسنجی ناموفق بود',
                    'data' => $e->errors(),
                ], 422);
            }
            return false;
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'true',
                    'code' => '03a' . 401,
                    'message' => 'لطفا وارد حساب کاربری خود شوید',
                    'data' => $e->getMessage(),
                ], 401);
            }
            return false;
        });

        $exceptions->render(function (UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'code' => '03e' . 403,
                    'message' => 'محدودیت دسترسی',
                    'data' => null,
                ], 400);
            }
            return false;
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'code' => '03m' . 404,
                    'message' => 'اطلاعات درخواستی یافت نشد',
                    'data' => null,
                ], 404);
            }
            return false;
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'code' => '03n' . 404,
                    'message' => 'مسیر درخواستی یافت نشد',
                    'data' => null,
                ], 404);
            }
            return false;
        });
    })->create();

