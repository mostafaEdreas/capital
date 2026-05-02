<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->input('idempotency_key');
    
        if (!$key) {
            return $next($request);
        }
    
        $lock = Cache::lock('req:' . $key, 20);
    
        if (! $lock->get()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'We are already processing your request.',
                ], 409);
            }
    
            return back()->withErrors('We are already processing your request.');
        }
    
        $response = null;
    
        try {
            $response = $next($request);
            return $response;
        } finally {
            // release on failures (API errors + web validation redirects)
            $shouldRelease =
                $response === null
                || $response->getStatusCode() >= 400
                || ($response->isRedirection() && session()->has('errors'));
    
            if ($shouldRelease) {
                optional($lock)->release();
            }
        }
    }
}