<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;

    protected $table = 'don_hang';
    protected $primaryKey = 'MaDonHang';

    protected $fillable = [
        'MaNguoiDung',
        'MaGiamGia',
        'PhiVanChuyen',
        'MaDoiTacVanChuyen',
        'TongTien',
        'PhuongThucThanhToan',
        'TrangThai',
        'TenKhachHang',
        'SoDienThoai',
        'DiaChiGiaoHang',
        'GhiChu',
        'ly_do_huy',
        'nguoi_huy',
        'XacNhanAt',
        'GiaoHangAt',
        'HoanThanhAt',
    ];

    protected $casts = [
        'TongTien' => 'decimal:2',
        'PhiVanChuyen' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'XacNhanAt' => 'datetime',
        'GiaoHangAt' => 'datetime',
        'HoanThanhAt' => 'datetime',
    ];

    // Relationships
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    public function chiTiet()
    {
        return $this->hasMany(DonHangChiTiet::class, 'MaDonHang', 'MaDonHang')
            ->with('monAn');
    }

    public function thanhToan()
    {
        return $this->hasOne(ThanhToan::class, 'MaDonHang', 'MaDonHang');
    }

    public function giamGia()
    {
        return $this->belongsTo(GiamGia::class, 'MaGiamGia', 'MaGiamGia');
    }

    public function doiTacVanChuyen()
    {
        return $this->belongsTo(DoiTacVanChuyen::class, 'MaDoiTacVanChuyen', 'id');
    }

    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'MaDonHang', 'MaDonHang');
    }

    // Accessors
    // Class Diagram Aliases
    public function getLyDoHuyAttribute()
    {
        return $this->attributes['ly_do_huy'] ?? null;
    }

    public function setLyDoHuyAttribute($value)
    {
        $this->attributes['ly_do_huy'] = $value;
    }

    public function getNguoiHuyAttribute()
    {
        return $this->attributes['nguoi_huy'] ?? null;
    }

    public function setNguoiHuyAttribute($value)
    {
        $this->attributes['nguoi_huy'] = $value;
    }
    public function getTongTienFormatAttribute()
    {
        return number_format((float)$this->TongTien, 0, ',', '.');
    }

    public function getTrangThaiColorAttribute()
    {
        return match ($this->TrangThai) {
            'Chờ xử lý'     => 'warning',
            'Đã xác nhận'   => 'success',
            'Đang chuẩn bị' => 'info',
            'Đang giao'     => 'primary',
            'Hoàn thành'    => 'success',
            'Hủy'           => 'danger',
            default         => 'secondary',
        };
    }

    public function getTrangThaiIconAttribute()
    {
        return match ($this->TrangThai) {
            'Chờ xử lý'     => 'fa-clock',
            'Đã xác nhận'   => 'fa-check-circle',
            'Đang chuẩn bị' => 'fa-utensils',
            'Đang giao'     => 'fa-shipping-fast',
            'Hoàn thành'    => 'fa-check-double',
            'Hủy'           => 'fa-times-circle',
            default         => 'fa-question-circle',
        };
    }

    // Helper methods
    public function canReview()
    {
        return $this->TrangThai === 'Hoàn thành';
    }

    public function canCancel()
    {
        return in_array($this->TrangThai, ['Chờ xử lý', 'Đã xác nhận']);
    }

    public function canConfirm()
    {
        return $this->TrangThai === 'Chờ xử lý';
    }

    // Scopes
    public function scopeOfUser($query, $userId)
    {
        return $query->where('MaNguoiDung', $userId);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('TrangThai', $status);
    }

    /**
     * Auto-cancel online orders that are pending and older than 15 minutes
     */
    public static function cancelExpiredOrders()
    {
        $expiredTime = now()->subMinutes(15);

        // Lấy các đơn hàng thanh toán online, đang chờ xử lý và tạo quá 15 phút
        $expiredOrders = self::where('PhuongThucThanhToan', 'Online')
            ->where('TrangThai', 'Chờ xử lý')
            ->where('created_at', '<=', $expiredTime)
            ->get();

        foreach ($expiredOrders as $order) {
            // Hủy đơn hàng
            $order->update([
                'TrangThai' => 'Hủy',
                'ly_do_huy' => 'Hệ thống tự động hủy do quá hạn thanh toán',
                'nguoi_huy' => 'system'
            ]);

            \Illuminate\Support\Facades\Log::info("Đơn hàng ORD" . str_pad($order->MaDonHang, 5, '0', STR_PAD_LEFT) . " đã bị hủy tự động do quá hạn 15 phút chưa thanh toán.");

            // Hủy trạng thái thanh toán và khóa link PayOS
            if ($order->thanhToan) {
                if ($order->thanhToan->payos_order_code && in_array($order->thanhToan->TrangThai, ['Chờ thanh toán', 'Thất bại'])) {
                    try {
                        $payOSService = app(\App\Services\PayOSService::class);
                        $payOSService->cancelPaymentLink((int)$order->thanhToan->payos_order_code, 'Quá hạn 15 phút không thanh toán');
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning("Không thể hủy link PayOS cho đơn {$order->MaDonHang}: " . $e->getMessage());
                    }
                }

                $order->thanhToan->update([
                    'TrangThai' => 'Thất bại'
                ]);
            }

            // Gửi thông báo đến khách hàng
            try {
                \App\Services\NotificationService::add(
                    $order->MaNguoiDung,
                    "Đơn hàng bị hủy tự động",
                    "Đơn hàng ORD" . str_pad($order->MaDonHang, 5, '0', STR_PAD_LEFT) . " đã bị hủy tự động do quá 15 phút chưa thanh toán.",
                    $order->MaDonHang
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Không thể gửi thông báo tự động hủy đơn {$order->MaDonHang}: " . $e->getMessage());
            }
        }
    }
}