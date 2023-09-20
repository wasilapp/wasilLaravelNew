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
        LaravelLocalization::setLocale('ar');
        if(isset($request->lang)&&$request->lang=='en')
        LaravelLocalization::setLocale('en');
        return $next($request);
    }
}
