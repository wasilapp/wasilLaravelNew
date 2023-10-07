<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ChangeLanguage
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        LaravelLocalization::setLocale('en');
        if(isset($request->lang)&&$request->lang=='ar')
        LaravelLocalization::setLocale('ar');
        return $next($request);
    }
}
