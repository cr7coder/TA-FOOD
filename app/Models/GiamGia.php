<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class GiamGia extends Model
{
    use HasFactory, SoftDeletes;

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
        'GioiHanNguoiDung',
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
        'GioiHanNguoiDung' => 'integer',
    ];

    // Relationships
    public function donHang()
    {
        return $this->hasMany(DonHang::class, 'MaGiamGia', 'MaGiamGia');
    }

    // Kiểm tra user cụ thể có được sử dụng voucher này không
    public function canUserUse($userId)
    {
        if (!$userId) {
            return true;
        }
        if ($this->GioiHanNguoiDung === null) {
            return true; // Không giới hạn
        }

        $usedCount = $this->donHang()
            ->where('MaNguoiDung', $userId)
            ->whereNotIn('TrangThai', ['Hủy']) // Loại trừ các đơn hàng đã bị hủy
            ->count();

        return $usedCount < $this->GioiHanNguoiDung;
    }

    // Kiểm tra voucher còn hiệu lực
    public function isActive()
    {
        $now = Carbon::now();
        return $this->NgayBatDau <= $now && $this->NgayKetThuc >= $now;
    }

    public function isValid()
    {
        return $this->isActive() && $this->hasQuantityAvailable();
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
            return min((float)$this->PhanTram, $tongTien);
        }

        if ($this->LoaiGiamGia === 'TienMat') {
            return min((float)$this->GiamToiDa, $tongTien);
        }

        // Loại phần trăm
        $discount = ($tongTien * (float)$this->PhanTram) / 100;

        if ($this->GiamToiDa && (float)$this->GiamToiDa > 0) {
            $discount = min($discount, (float)$this->GiamToiDa);
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

    public function getTimeRemainingColor()
    {
        if ($this->isExpired()) {
            return 'danger';
        }
        $now = Carbon::now();
        $endDate = Carbon::parse($this->NgayKetThuc);
        if ($now->diffInDays($endDate) <= 3) {
            return 'warning';
        }
        return 'success';
    }

    public function getTimeRemainingShort()
    {
        return $this->getTimeRemaining();
    }

    public function getPhanTramFormatAttribute()
    {
        if ($this->LoaiGiamGia === 'Số tiền') {
            return number_format((float)$this->PhanTram, 0, ',', '.') . 'đ';
        }
        if ($this->LoaiGiamGia === 'TienMat') {
            return number_format((float)$this->GiamToiDa, 0, ',', '.') . 'đ';
        }
        return ((float)$this->PhanTram) . '%';
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

    public function scopeExpired($query)
    {
        return $query->where('NgayKetThuc', '<', Carbon::now());
    }

    public function scopeExpiringSoon($query)
    {
        $now = Carbon::now();
        $soon = Carbon::now()->addDays(3);
        return $query->where('NgayKetThuc', '>=', $now)
            ->where('NgayKetThuc', '<=', $soon);
    }
}