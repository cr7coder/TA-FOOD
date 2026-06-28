<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class SettingService
{
    protected static $settings = null;

    /**
     * Lấy giá trị cấu hình theo Key
     */
    public static function get($key, $default = null)
    {
        if (self::$settings === null) {
            $path = storage_path('app/system_settings.json');
            $defaults = [
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

            if (File::exists($path)) {
                $saved = json_decode(File::get($path), true);
                if (is_array($saved)) {
                    $defaults = array_merge($defaults, $saved);
                }
            }
            self::$settings = $defaults;
        }

        return self::$settings[$key] ?? $default;
    }

    /**
     * Kiểm tra nhanh một cấu hình dạng boolean
     */
    public static function check($key, $default = false)
    {
        return filter_var(self::get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }
}
