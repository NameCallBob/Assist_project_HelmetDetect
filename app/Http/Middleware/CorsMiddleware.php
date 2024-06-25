<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
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
        $headers = [
            // 'Access-Control-Allow-Origin' => 'http://localhost:3000',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true', //避免cors先修改為false
            'Access-Control-Max-Age' => '5',
            // 'Access-Control-Allow-Headers' => 'Accept,Content-Type,Authorization,X-Requested-With,Ngrok-Skip-Browser-Warning',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            // 'Access-Control-Allow-Headers' => $request->header('Access-Control-Request-Headers')

        ];

        if ($request->isMethod('OPTIONS')) {
            // $headers['Access-Control-Allow-Headers'] = $request->header('Access-Control-Request-Headers');
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
        // if ($request->getMethod() == "OPTIONS") {
        //     return response('', 200)
        //         ->header('Access-Control-Allow-Origin', '*')
        //         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        //         ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning');
        // }
        // $response = $next($request);

        // $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
        // $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // // 在此處加入 ngrok-skip-browser-warning
        // $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, ngrok-skip-browser-warning');

        // return $response;

    }
}