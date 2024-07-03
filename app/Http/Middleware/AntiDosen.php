<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AntiDosen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->user()){
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized, please login first.'
            ], 401);
        }

        if($request->user()->role === 'dosen'){
            return response()->json([
                'message' => "You can't perform this action.",
            ], 403);
        }
        return $next($request);
    }
}
