<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class NotificationService
{
    private static function getPath($userId)
    {
        return storage_path("app/notifications_{$userId}.json");
    }

    /**
     * Lấy danh sách thông báo của người dùng
     */
    public static function get($userId)
    {
        $path = self::getPath($userId);
        if (!File::exists($path)) {
            return [];
        }
        $data = json_decode(File::get($path), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Thêm thông báo mới cho người dùng
     */
    public static function add($userId, $title, $message, $orderId = null, $targetUrl = null)
    {
        // Kiểm tra xem admin có bật cấu hình notify_order trong cài đặt hệ thống hay không
        if (!SettingService::check('notify_order', true)) {
            return null;
        }

        $notifications = self::get($userId);

        $newNotification = [
            'id' => uniqid(),
            'title' => $title,
            'message' => $message,
            'order_id' => $orderId,
            'target_url' => $targetUrl,
            'is_read' => false,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'time_diff' => 'Vừa xong'
        ];

        // Đẩy lên đầu danh sách
        array_unshift($notifications, $newNotification);

        // Giới hạn tối đa 30 thông báo gần nhất
        $notifications = array_slice($notifications, 0, 30);

        $path = self::getPath($userId);
        $dir = dirname($path);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($path, json_encode($notifications, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $newNotification;
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public static function markAsRead($userId, $id)
    {
        $notifications = self::get($userId);
        foreach ($notifications as &$n) {
            if ($n['id'] === $id) {
                $n['is_read'] = true;
                break;
            }
        }
        File::put(self::getPath($userId), json_encode($notifications, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Đánh dấu toàn bộ thông báo là đã đọc
     */
    public static function markAllAsRead($userId)
    {
        $notifications = self::get($userId);
        foreach ($notifications as &$n) {
            $n['is_read'] = true;
        }
        File::put(self::getPath($userId), json_encode($notifications, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
