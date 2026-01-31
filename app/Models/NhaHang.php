<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaHang extends Model
{
    use HasFactory;

    protected $table = 'nha_hang';
    protected $primaryKey = 'MaNhaHang';

    protected $fillable = [
        'TenNhaHang',
        'DiaChi',
        'SoDienThoai',
        'GioMoCua',
        'GioDongCua',
        'MaNguoiDung',
    ];

    protected $casts = [
        'GioMoCua' => 'datetime:H:i',
        'GioDongCua' => 'datetime:H:i',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    public function monAn()
    {
        return $this->hasMany(MonAn::class, 'MaNhaHang', 'MaNhaHang');
    }

    public function monAnDangBan()
    {
        return $this->hasMany(MonAn::class, 'MaNhaHang', 'MaNhaHang')
            ->where('TrangThai', 'Còn bán');
    }

    // Accessors
    public function getGioMoCuaForFormAttribute(): string
    {
        return $this->GioMoCua ? substr($this->GioMoCua, 0, 5) : '';
    }

    public function getGioDongCuaForFormAttribute(): string
    {
        return $this->GioDongCua ? substr($this->GioDongCua, 0, 5) : '';
    }

    // Helper methods
    public function isOpen()
    {
        if (!$this->GioMoCua || !$this->GioDongCua) {
            return true; // Nếu không set giờ thì coi như luôn mở
        }

        $now = now()->format('H:i:s');
        return $now >= $this->GioMoCua && $now <= $this->GioDongCua;
    }

    public function getTongMonAnAttribute()
    {
        return $this->monAn()->count();
    }
}