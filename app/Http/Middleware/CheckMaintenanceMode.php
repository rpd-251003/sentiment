<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceMode = \App\Models\AppSetting::get('maintenance_mode', '0');

        // Jika maintenance mode aktif
        if ($maintenanceMode == '1') {
            $user = auth()->user();

            // Skip maintenance untuk admin dan kaprodi
            if ($user && $user->isAdminOrKaprodi()) {
                return $next($request);
            }

            // Tampilkan halaman maintenance untuk role lainnya
            return response()->view('maintenance');
        }

        return $next($request);
    }
}
