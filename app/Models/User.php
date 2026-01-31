<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nguoi_dung';
    protected $primaryKey = 'MaNguoiDung';

    protected $fillable = [
        'TenDangNhap',
        'MatKhau',
        'HoTen',
        'Email',
        'SoDienThoai',
        'VaiTro',
    ];

    protected $hidden = [
        'MatKhau',
        'remember_token',
    ];

    // Disable remember token functionality
    public function getRememberTokenName()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Do nothing
    }

    public function getRememberToken()
    {
        return null;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'MatKhau' => 'hashed',
        ];
    }

    // Override authentication methods
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    public function getEmailForPasswordReset()
    {
        return $this->Email;
    }

    // Accessors
    public function getNameAttribute()
    {
        return $this->HoTen;
    }

    public function getEmailAttribute($value)
    {
        return $this->attributes['Email'];
    }

    // Relationships
    public function gioHang()
    {
        return $this->hasOne(GioHang::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    public function donHang()
    {
        return $this->hasMany(DonHang::class, 'MaNguoiDung', 'MaNguoiDung')
            ->orderBy('created_at', 'desc');
    }

    public function nhaHang()
    {
        return $this->hasOne(NhaHang::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'ma_nguoi_dung', 'MaNguoiDung');
    }

    // Scopes
    public function scopeKhachHang($query)
    {
        return $query->where('VaiTro', 'KhachHang');
    }

    public function scopeQuanTri($query)
    {
        return $query->where('VaiTro', 'QuanTri');
    }

    public function scopeNguoiBan($query)
    {
        return $query->where('VaiTro', 'NguoiBan');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->VaiTro === 'QuanTri';
    }

    public function isSeller()
    {
        return $this->VaiTro === 'NguoiBan';
    }

    public function isCustomer()
    {
        return $this->VaiTro === 'KhachHang';
    }

    // Get cart with items
    public function getOrCreateCart()
    {
        return $this->gioHang ?? $this->gioHang()->create([
            'MaNguoiDung' => $this->MaNguoiDung
        ]);
    }
}