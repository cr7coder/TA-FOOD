<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GiamGia extends Model
{
    use HasFactory;

    protected $table = 'giam_gia';
    protected $primaryKey = 'MaGiamGia';

    protected $fillable = [
        'MaCode',
        'LoaiGiamGia',
        'PhanTram',
        'GiamToiDa',
        'DonHangToiThieu',
        'NgayBatDau',
        'NgayKetThuc',
        'SoLuongToiDa',
        'SoLuongDaSuDung',
        'MoTa',
    ];

    protected $casts = [
        'NgayBatDau' => 'date',
        'NgayKetThuc' => 'date',
        'PhanTram' => 'decimal:2',
        'DonHangToiThieu' => 'decimal:2',
        'GiamToiDa' => 'decimal:2',
        'SoLuongToiDa' => 'integer',
        'SoLuongDaSuDung' => 'integer',
    ];

    // Relationships
    public function donHang()
    {
        return $this->hasMany(DonHang::class, 'MaGiamGia', 'MaGiamGia');
    }

    // Kiểm tra voucher còn hiệu lực
    public function isActive()
    {
        $now = Carbon::now();
        return $this->NgayBatDau <= $now && $this->NgayKetThuc >= $now;
    }

    public function isValid()
    {
        return $this->isActive();
    }

    public function isExpired()
    {
        return Carbon::now() > $this->NgayKetThuc;
    }

    // Kiểm tra còn số lượng
    public function hasQuantityAvailable()
    {
        if ($this->SoLuongToiDa === null) {
            return true; // Không giới hạn
        }
        return $this->SoLuongDaSuDung < $this->SoLuongToiDa;
    }

    // Kiểm tra đơn hàng đạt điều kiện
    public function meetsMinimumOrder($tongTien)
    {
        return $tongTien >= $this->DonHangToiThieu;
    }

    // Tính số tiền giảm
    public function calculateDiscount($tongTien)
    {
        if (!$this->meetsMinimumOrder($tongTien)) {
            return 0;
        }

        if ($this->LoaiGiamGia === 'Số tiền') {
            return min($this->PhanTram, $tongTien);
        }

        // Loại phần trăm
        $discount = ($tongTien * $this->PhanTram) / 100;

        if ($this->GiamToiDa) {
            $discount = min($discount, $this->GiamToiDa);
        }

        return $discount;
    }

    // Tăng số lượng đã sử dụng
    public function incrementUsage()
    {
        $this->increment('SoLuongDaSuDung');
    }

    // Giảm số lượng đã sử dụng (khi hủy đơn)
    public function decrementUsage()
    {
        if ($this->SoLuongDaSuDung > 0) {
            $this->decrement('SoLuongDaSuDung');
        }
    }

    // Accessors
    public function getTimeRemaining()
    {
        $now = Carbon::now();
        $endDate = Carbon::parse($this->NgayKetThuc)->endOfDay();

        if ($now > $endDate) {
            return 'Hết hạn';
        }

        $diff = $now->diff($endDate);
        $result = [];

        if ($diff->days > 0) {
            $result[] = $diff->days . ' ngày';
        }
        if ($diff->h > 0) {
            $result[] = $diff->h . ' giờ';
        }
        if ($diff->i > 0) {
            $result[] = $diff->i . ' phút';
        }

        return empty($result) ? 'Dưới 1 phút' : implode(' ', $result);
    }

    public function getPhanTramFormatAttribute()
    {
        if ($this->LoaiGiamGia === 'Số tiền') {
            return number_format($this->PhanTram, 0, ',', '.') . 'đ';
        }
        return $this->PhanTram . '%';
    }

    // Scopes
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('NgayBatDau', '<=', $now)
            ->where('NgayKetThuc', '>=', $now);
    }

    public function scopeAvailable($query)
    {
        $now = Carbon::now();
        return $query->where('NgayBatDau', '<=', $now)
            ->where('NgayKetThuc', '>=', $now)
            ->where(function($q) {
                $q->whereNull('SoLuongToiDa')
                  ->orWhereRaw('SoLuongDaSuDung < SoLuongToiDa');
            });
    }
}