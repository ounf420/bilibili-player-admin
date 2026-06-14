<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * If the installed.lock file exists in storage, allow normal access.
     * If it doesn't exist and the request is not for /install*, redirect to /install.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lockFile = storage_path('installed.lock');

        if (file_exists($lockFile)) {
            return $next($request);
        }

        // Allow access to installation routes
        if ($request->is('install*')) {
            return $next($request);
        }

        // Not installed and not on install route — redirect to installer
        return redirect('/install');
    }
}
