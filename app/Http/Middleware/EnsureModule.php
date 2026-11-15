<?php

namespace App\Http\Middleware;

use App\Support\Modules;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModule
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! Modules::enabled($module)) {
            abort(404);
        }

        return $next($request);
    }
}
