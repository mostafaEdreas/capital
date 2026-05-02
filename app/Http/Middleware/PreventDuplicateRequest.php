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
    
        // If we can't get the lock, it's a duplicate
        if (!$lock->get()) {
            if($request->isJson() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'We are already processing your request.',
                ], 400);
            }
            return back()->withErrors('We are already processing your request.');
        }
    
        // DO NOT release the lock in a finally block if you want to 
        // block submissions for the full 10 seconds.
        $response = $next($request);
        return $response;
    }
}