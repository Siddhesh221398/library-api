<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Request:', [
            'method' => $request->method(),
            'url' => $request->url(),
            'data' => $request->all(),
        ]);

        return $next($request);
    }
}
