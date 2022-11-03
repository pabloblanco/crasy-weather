<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//use App\Models\AuthorizedLogs;
use App\Models\AuthorizedTokens;

class AuthorizedToken {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {

        if (AuthorizedTokens::isAuthorizedToken($request))
          return $next($request);
        else {

          $msg = 'Intento de conexion desde IP: ' . $request->ip() . ' Token: ' . $request->bearerToken() . ' Ambiente: ' . env('APP_ENV');
          //AuthorizedLogs::saveLogBD(false, false, false, false, 'INFO', $msg);
          return response('Combination Token-Origin is invalid ', 401);

        }  

    }

}