<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    use HasFactory;

    protected $table = 'binh_luans';

    protected $fillable = [
        'MaMonAn',
        'MaNguoiDung',
        'MaDonHang',
        'diem_danh_gia',
        'noi_dung',
        'hinh_anh',
        'da_mua',
        'trang_thai',
        'phan_hoi',
        'phan_hoi_at',
    ];

    protected $casts = [
        'diem_danh_gia' => 'integer',
        'da_mua' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'phan_hoi_at' => 'datetime',
    ];

    // Relationships
    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'MaMonAn', 'MaMonAn');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    // Accessors
    public function getDiemSaoHtmlAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->diem_danh_gia) {
                $stars .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $stars .= '<i class="far fa-star text-warning"></i>';
            }
        }
        return $stars;
    }

    // Class Diagram Aliases
    public function getMaBinhLuanAttribute()
    {
        return $this->attributes['id'] ?? $this->id;
    }

    public function getDiemDanhGiaAttribute()
    {
        return $this->attributes['diem_danh_gia'] ?? null;
    }

    public function setDiemDanhGiaAttribute($value)
    {
        $this->attributes['diem_danh_gia'] = $value;
    }

    public function getDaMuaAttribute()
    {
        return $this->attributes['da_mua'] ?? null;
    }

    public function setDaMuaAttribute($value)
    {
        $this->attributes['da_mua'] = $value;
    }

    public function getNoiDungAttribute()
    {
        return $this->attributes['noi_dung'] ?? null;
    }

    public function setNoiDungAttribute($value)
    {
        $this->attributes['noi_dung'] = $value;
    }

    public function getPhanHoiAttribute()
    {
        return $this->attributes['phan_hoi'] ?? null;
    }

    public function setPhanHoiAttribute($value)
    {
        $this->attributes['phan_hoi'] = $value;
    }

    public function getPhanHoiAtAttribute($value)
    {
        $val = $this->attributes['phan_hoi_at'] ?? $value;
        return $val ? \Carbon\Carbon::parse($val) : null;
    }

    public function setPhanHoiAtAttribute($value)
    {
        $this->attributes['phan_hoi_at'] = $value ? \Carbon\Carbon::parse($value) : null;
    }

    public function getHinhAnhAttribute($value)
    {
        return $this->attributes['hinh_anh'] ?? $value;
    }

    public function setHinhAnhAttribute($value)
    {
        $this->attributes['hinh_anh'] = $value;
    }

    public function getTrangThaiAttribute()
    {
        return $this->attributes['trang_thai'] ?? null;
    }

    public function setTrangThaiAttribute($value)
    {
        $this->attributes['trang_thai'] = $value;
    }

    // Scopes
    public function scopeDaDuyet($query)
    {
        return $query->where('trang_thai', 'Đã duyệt');
    }

    public function scopeChoDuyet($query)
    {
        return $query->where('trang_thai', 'Chờ duyệt');
    }

    public function scopeDaMua($query)
    {
        return $query->where('da_mua', true);
    }

    // Helper methods
    public static function hasReviewed($maNguoiDung, $maMonAn, $maDonHang)
    {
        return self::where('MaNguoiDung', $maNguoiDung)
            ->where('MaMonAn', $maMonAn)
            ->where('MaDonHang', $maDonHang)
            ->exists();
    }
}