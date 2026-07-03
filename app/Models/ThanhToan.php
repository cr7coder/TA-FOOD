<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';
    protected $primaryKey = 'MaThanhToan';

    protected $fillable = [
        'MaDonHang',
        'SoTien',
        'PhuongThuc',
        'TrangThai',
        'MaGiaoDich',
        'NgayThanhToan',
        'payos_order_code',
        'minh_chung_thanh_toan',
    ];

    protected $casts = [
        'SoTien'        => 'decimal:2',
        'NgayThanhToan' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // Relationships
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'MaDonHang', 'MaDonHang');
    }

    // Accessors
    // Class Diagram Aliases
    public function getPayosOrderCodeAttribute()
    {
        return $this->attributes['payos_order_code'] ?? null;
    }

    public function setPayosOrderCodeAttribute($value)
    {
        $this->attributes['payos_order_code'] = $value;
    }

    public function getMinhChungThanhToanAttribute()
    {
        return $this->attributes['minh_chung_thanh_toan'] ?? null;
    }

    public function setMinhChungThanhToanAttribute($value)
    {
        $this->attributes['minh_chung_thanh_toan'] = $value;
    }
    public function getSoTienFormatAttribute()
    {
        return number_format((float)$this->SoTien, 0, ',', '.');
    }

    public function getTrangThaiColorAttribute()
    {
        return match ($this->TrangThai) {
            'Chờ thanh toán' => 'warning',
            'Chờ xác nhận'   => 'info',
            'Đã thanh toán'  => 'success',
            'Thất bại'       => 'danger',
            'Đã hoàn tiền'   => 'info',
            default          => 'secondary',
        };
    }

    // Helper methods
    public function isSuccess()
    {
        return $this->TrangThai === 'Đã thanh toán';
    }

    public function isPending()
    {
        return $this->TrangThai === 'Chờ thanh toán';
    }

    public function isPendingConfirmation()
    {
        return $this->TrangThai === 'Chờ xác nhận';
    }

    public function isFailed()
    {
        return $this->TrangThai === 'Thất bại';
    }
}