<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    use HasFactory;

    protected $table = 'binh_luans';

    protected $fillable = [
        'ma_mon_an',
        'ma_nguoi_dung',
        'diem_danh_gia',
        'noi_dung',
        'hinh_anh',
        'da_mua',
        'trang_thai',
    ];

    protected $casts = [
        'diem_danh_gia' => 'integer',
        'da_mua' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'ma_mon_an', 'MaMonAn');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'ma_nguoi_dung', 'MaNguoiDung');
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
}