<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class LogRoutes
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if (!config('logging.channels.routes.active')) {
            return $response;
        }

        if ($request->isMethod('GET')) {
            return $response;
        }

        $log = [
            'IP' => $request->ip(),
            'URI' => $request->getUri(),
            'METHOD' => $request->getMethod(),
            'USER' => $request->user()?->only('id', 'email', 'phone'),
            'BODY' => Arr::except($request->input(), ['password', 'confirm_password', '_destination', '_method', '_token', '_modal', 'destination']),
            //'RESPONSE' => $response->getContent(),
        ];

        Log::channel('routes')->info(json_encode($log, JSON_UNESCAPED_SLASHES));

        return $response;
    }
}
