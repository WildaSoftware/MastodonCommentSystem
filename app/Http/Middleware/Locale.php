<?php

namespace App\Http\Middleware;

use App\Http\Support\Utility;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Locale {

	public function handle(Request $request, Closure $next) {
		$session = $request->session();

		if(empty($session->get(Utility::SESSION_KEY))) {
			$browserLanguage = null;
			if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			}

			if(!empty($browserLanguage) && in_array($browserLanguage, Utility::LOCALES)) {
				$session->put(Utility::SESSION_KEY, $browserLanguage);
			} 
			else {
				$session->put(Utility::SESSION_KEY, 'pl');
			}
		}

        app()->setlocale($session->get(Utility::SESSION_KEY));

		return $next($request);
	}
}