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
        'MaDoiTacVanChuyen',
        'TongTien',
        'PhuongThucThanhToan',
        'TrangThai',
        'TenKhachHang',
        'SoDienThoai',
        'DiaChiGiaoHang',
        'GhiChu',
    ];

    protected $casts = [
        'TongTien' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->hasMany(BinhLuan::class, 'ma_don_hang', 'MaDonHang');
    }

    // Accessors
    public function getTongTienFormatAttribute()
    {
        return number_format((float)$this->TongTien, 0, ',', '.');
    }

    public function getTrangThaiColorAttribute()
    {
        return match ($this->TrangThai) {
            'Chờ xử lý'     => 'warning',
            'Đã thanh toán' => 'success',
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
            'Đã thanh toán' => 'fa-check-circle',
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
        return in_array($this->TrangThai, ['Chờ xử lý', 'Đã thanh toán']);
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
}