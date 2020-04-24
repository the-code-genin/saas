<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthenticationError;
use Closure;
use App\Helpers\Api;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $type
     * @return mixed
     */
    public function handle($request, Closure $next, string $type)
    {
        if ($request->user()->user_type != $type) {
            throw new AuthenticationError('This user can not access this route.');
        }

        return $next($request);
    }
}
