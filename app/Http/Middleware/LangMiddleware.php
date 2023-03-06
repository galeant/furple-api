<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class LangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = 'id';
        $availableLang = ['id', 'en'];
        if (in_array($request->header('lang'), $availableLang)) {
            $lang = $request->header('lang');
        }
        App::setLocale($lang);
        return $next($request);
    }
}
