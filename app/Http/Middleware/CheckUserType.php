<?php

namespace App\Http\Middleware;

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
            return Api::generateErrorResponse(401, 'AuthenticationError', 'This user can not access this route.');
        }

        return $next($request);
    }
}
