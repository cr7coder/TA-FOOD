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
        'Email',
        'GioMoCua',
        'GioDongCua',
        'MaNguoiDung',
        'latitude',
        'longitude',
        'phi_ship_co_ban',
        'phi_ship_moi_km',
        'km_mien_phi',
        'commission_rate',
        'HinhAnh',
        'TrangThai',
    ];

    // Casting removed for better TIME column compatibility

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

    public function getHinhAnhUrlAttribute()
    {
        if ($this->HinhAnh) {
            if (filter_var($this->HinhAnh, FILTER_VALIDATE_URL)) {
                return $this->HinhAnh;
            }
            return asset('storage/' . $this->HinhAnh);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->TenNhaHang) . '&background=ffbe33&color=222831&size=128&bold=true';
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

    public function scopeHoatDong($query)
    {
        return $query->where('TrangThai', 'Hoạt động');
    }
}