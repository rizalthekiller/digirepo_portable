<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the application is not installed and we're not on the install page, redirect to install
        if (!$this->isInstalled() && !$request->is('install*') && !$request->is('_debugbar*')) {
            return redirect()->route('install.index');
        }

        // If the application is already installed and we try to access the install page, redirect to home
        if ($this->isInstalled() && $request->is('install*')) {
            return redirect()->route('home');
        }

        return $next($request);
    }

    /**
     * Check if the application is installed.
     *
     * @return bool
     */
    protected function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }
}
