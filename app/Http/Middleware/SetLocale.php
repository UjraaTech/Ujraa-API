<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language');
        
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        } else {
            app()->setLocale('ar'); // Default to Arabic
        }

        return $next($request);
    }
}