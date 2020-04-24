<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Api;
use App\Exceptions\AuthenticationError;

class CheckUserVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->verified == false) {
            throw new AuthenticationError('User must be verified to use this route.');
        }

        return $next($request);
    }
}
