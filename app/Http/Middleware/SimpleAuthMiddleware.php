<?php namespace App\Http\Middleware;

use Closure;
use App\Http\Service\AuthenticationService;
use App;

class SimpleAuthMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $auth =  $request->header('Authorization');
        return AuthenticationService::authenticate($auth) ? $next($request) : App::abort(403, 'Unauthorized action!');
    }

}