<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if (!auth()->check() || auth()->user()->role !== 'user') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Only normal users allowed.'
            ], 403);
        }

        return $next($request);
    }
}