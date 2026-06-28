<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo của khách hàng
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đăng nhập.'
            ], 401);
        }

        $userId = $user->MaNguoiDung;
        $notifications = NotificationService::get($userId);

        // Định dạng thời gian thân thiện (Vietnamese diffForHumans)
        Carbon::setLocale('vi');
        foreach ($notifications as &$n) {
            if (isset($n['created_at'])) {
                $n['time_diff'] = Carbon::parse($n['created_at'])->diffForHumans();
            }
        }

        $unreadCount = collect($notifications)->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function readAll()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đăng nhập.'
            ], 401);
        }

        NotificationService::markAllAsRead($user->MaNguoiDung);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu đọc toàn bộ thông báo.'
        ]);
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function read($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đăng nhập.'
            ], 401);
        }

        NotificationService::markAsRead($user->MaNguoiDung, $id);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu đọc thông báo.'
        ]);
    }
}

