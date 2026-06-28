<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập.'
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->VaiTro !== $role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện hành động này.'
                ], 403);
            }

            // Chuyển hướng hợp lý dựa trên vai trò thực sự của họ
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            } elseif ($user->isSeller()) {
                return redirect()->route('seller.dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }

            return redirect()->route('foods.index')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
