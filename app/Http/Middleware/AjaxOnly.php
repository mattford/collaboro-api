<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxOnly
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->wantsJson()) {
            // Handle the non-ajax request
            return response()->noContent(Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
