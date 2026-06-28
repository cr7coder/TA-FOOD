<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    private function checkAdmin()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Quyền truy cập bị từ chối.'
            ], 403);
        }
        return null;
    }

    private function getSettingsPath()
    {
        return storage_path('app/system_settings.json');
    }

    private function getDefaultSettings()
    {
        return [
            // Cài đặt chung
            'site_name' => 'CabaFood',
            'site_desc' => 'Nền tảng đặt món ăn trực tuyến hàng đầu Việt Nam',
            'support_email' => 'support@cabafood.vn',
            'hotline' => '1900-xxxx',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'language' => 'vi',
            'currency' => 'VND',

            // Cài đặt thông báo
            'notify_email' => true,
            'notify_sms' => true,
            'notify_push' => true,
            'notify_order' => true,
            'notify_promo' => false,
            'notify_system' => true,

            // Cài đặt thanh toán
            'payment_cod' => true,
            'payment_momo' => true,
            'payment_vnpay' => true,
            'payment_zalopay' => false,
            'payment_transfer' => true,
            'order_min' => 20000,
            'order_max' => 5000000,
            'shipping_fee' => 15000,

            // Cài đặt bảo mật
            'security_2fa' => false,
            'session_timeout' => 30,
            'max_login_fail' => 5,
            'password_expire' => 90,
            'ip_whitelist' => '',

            // Cài đặt ứng dụng
            'app_maintenance' => false,
            'app_allow_register' => true,
            'app_email_verify' => true,
            'app_guest_checkout' => false,
            'app_show_reviews' => true,
            'app_auto_accept' => false,
        ];
    }

    public function show()
    {
        if ($response = $this->checkAdmin()) return $response;

        $path = $this->getSettingsPath();
        $settings = $this->getDefaultSettings();

        if (File::exists($path)) {
            $savedSettings = json_decode(File::get($path), true);
            if (is_array($savedSettings)) {
                $settings = array_merge($settings, $savedSettings);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function update(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $path = $this->getSettingsPath();
        $settings = $this->getDefaultSettings();

        if (File::exists($path)) {
            $savedSettings = json_decode(File::get($path), true);
            if (is_array($savedSettings)) {
                $settings = array_merge($settings, $savedSettings);
            }
        }

        // Cập nhật các trường gửi lên
        foreach ($this->getDefaultSettings() as $key => $defaultVal) {
            if ($request->has($key)) {
                $val = $request->input($key);
                if (is_bool($defaultVal)) {
                    $settings[$key] = filter_var($val, FILTER_VALIDATE_BOOLEAN);
                } elseif (is_numeric($defaultVal)) {
                    $settings[$key] = (float) $val;
                } else {
                    $settings[$key] = (string) $val;
                }
            }
        }

        // Tạo thư mục storage/app nếu chưa tồn tại
        $dir = dirname($path);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($path, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật cấu hình hệ thống thành công.',
            'data' => $settings
        ]);
    }
}
