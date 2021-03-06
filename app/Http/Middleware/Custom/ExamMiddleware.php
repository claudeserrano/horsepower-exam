<?php

namespace App\Http\Middleware\Custom;

use Closure;

class ExamMiddleware
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
        if(!$request->session()->has('progress')){
            session(['progress' => 0]);
        }
        
        return $next($request);
    }
}
