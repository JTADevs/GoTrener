<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLogged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!session('loggedUser.uid'))
        {
            return redirect()->route('auth')->with(['loginError'=>'Zaloguj się aby otrzymać dostęp do tego zasobu.']);
        }
        return $next($request);
    }
}
