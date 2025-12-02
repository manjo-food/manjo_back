<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class CustomThrottle extends ThrottleRequests
{
    /**
     * Resolve the rate limiting signature for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        // Use user ID if authenticated, otherwise use IP address
        $identifier = $request->user()?->id ?: $request->ip();

        return sha1($identifier);
    }

    /**
     * Get the cache store instance.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function store()
    {
        // Use configurable cache store for rate limiting (defaults to redis)
        $store = env('THROTTLE_CACHE_STORE', 'redis');
        return Cache::store($store);
    }

    /**
     * Create a 'too many attempts' exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  callable|null  $responseCallback
     * @return \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return new ThrottleRequestsException(
            'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً کمی صبر کنید.',
            null,
            $headers
        );
    }

    /**
     * Handle a passed validation attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  float|int  $decayMinutes
     * @param  string  $prefix
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        // Set different limits based on authentication status
        if ($request->user()) {
            // Authenticated user: 100 requests per minute
            $maxAttempts = 100;
        } else {
            // Anonymous user (IP-based): 30 requests per minute
            $maxAttempts = 30;
        }

        try {
            return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
        } catch (ThrottleRequestsException $e) {
            return response()->json([
                'error' => 'true',
                'code' => '03t' . 429,
                'message' => 'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً کمی صبر کنید.',
                'data' => null,
            ], 429, $e->getHeaders());
        }
    }
}
