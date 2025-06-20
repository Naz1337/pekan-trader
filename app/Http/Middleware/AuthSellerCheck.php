<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSellerCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->seller()->exists()) {
            return redirect()->route('home');
        }

        $seller = auth()->user()->seller;
        if (!$seller->approved) {
            return redirect()->route('seller.pending');
        }

        return $next($request);
    }
}
