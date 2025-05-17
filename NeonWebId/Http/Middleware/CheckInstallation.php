<?php

namespace Modules\NeonWebId\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * CheckInstallation Middleware
 * @created 2025-05-17 07:14:20
 * @author wichaksono
 */
class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Jika sudah mengakses route installer, biarkan
        if ($request->is('install*')) {
            return $next($request);
        }

        // Jika belum terinstall, redirect ke installer
        if ( ! file_exists(storage_path('installed'))) {
            return redirect()->route('installer.index');
        }

        return $next($request);
    }
}
