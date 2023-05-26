<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Support\Utility;
use Illuminate\Support\Facades\App;

class SetLanguageMiddleware {

    public function handle($request, Closure $next) {
        // TODO: W sumie dlaczego mamy podobną funkcjonalność w Locale i tutaj?
        $lang = $request->route()->parameter('lang');

        if(!empty($lang) && in_array($lang, Utility::LOCALES)) {
    
            if(!empty($request)) {
                $request->session()->put(Utility::SESSION_KEY, $lang);
            }
    
            App::setLocale($lang);
        }

        return $next($request);
    }
}