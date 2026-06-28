<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SettingService;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if maintenance mode is enabled in system settings
        if (SettingService::check('app_maintenance', true)) {
            $path = $request->path();

            // Exclude admin pages, api/v1/admin, login, logout, and the maintenance route itself to prevent infinite loop
            if (
                str_starts_with($path, 'admin') || 
                str_contains($path, 'api/v1/admin') || 
                $path === 'maintenance' || 
                $path === 'login' || 
                $path === 'logout'
            ) {
                return $next($request);
            }

            return redirect()->route('maintenance');
        }

        return $next($request);
    }
}
