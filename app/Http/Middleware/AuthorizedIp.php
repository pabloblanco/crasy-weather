<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuthorizedIps;
//use App\Models\AuthorizedLogs;

class AuthorizedIp {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {

        if (AuthorizedIps::isIpAuthorized($request->ip()))
          return $next($request);
        else {

          $msg = 'Intento de conexion desde: ' . $request->ip();
          //AuthorizedLogs::saveLogBD(false, false, false, false, 'INFO', $msg);
          return response('Origin Not authorized', 401);

        }    

    }

}