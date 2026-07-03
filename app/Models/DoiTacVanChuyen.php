<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoiTacVanChuyen extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'doi_tac_van_chuyens';

    protected $fillable = [
        'ten_doi_tac',
        'so_dien_thoai',
        'dia_chi_tru_so',
        'email_lien_he',
        'phi_van_chuyen',
        'phi_km',
        'nguoi_lien_he',
        'trang_thai',
    ];

    protected $casts = [
        'phi_van_chuyen' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function donHang()
    {
        return $this->hasMany(DonHang::class, 'MaDoiTacVanChuyen', 'id');
    }

    // Accessors
    public function getPhiVanChuyenFormatAttribute()
    {
        return number_format((float)$this->phi_van_chuyen, 0, ',', '.');
    }

    // Class Diagram Aliases
    public function getMaDoiTacAttribute()
    {
        return $this->attributes['id'] ?? $this->id;
    }

    public function getTenDoiTacAttribute()
    {
        return $this->attributes['ten_doi_tac'] ?? null;
    }

    public function setTenDoiTacAttribute($value)
    {
        $this->attributes['ten_doi_tac'] = $value;
    }

    public function getNguoiLienHeAttribute()
    {
        return $this->attributes['nguoi_lien_he'] ?? null;
    }

    public function setNguoiLienHeAttribute($value)
    {
        $this->attributes['nguoi_lien_he'] = $value;
    }

    public function getSoDienThoaiAttribute()
    {
        return $this->attributes['so_dien_thoai'] ?? null;
    }

    public function setSoDienThoaiAttribute($value)
    {
        $this->attributes['so_dien_thoai'] = $value;
    }

    public function getDiaChiTruSoAttribute()
    {
        return $this->attributes['dia_chi_tru_so'] ?? null;
    }

    public function setDiaChiTruSoAttribute($value)
    {
        $this->attributes['dia_chi_tru_so'] = $value;
    }

    public function getEmailLienHeAttribute()
    {
        return $this->attributes['email_lien_he'] ?? null;
    }

    public function setEmailLienHeAttribute($value)
    {
        $this->attributes['email_lien_he'] = $value;
    }

    public function getPhiVanChuyenAttribute()
    {
        return $this->attributes['phi_van_chuyen'] ?? null;
    }

    public function setPhiVanChuyenAttribute($value)
    {
        $this->attributes['phi_van_chuyen'] = $value;
    }

    public function getPhiKmAttribute()
    {
        return $this->attributes['phi_km'] ?? null;
    }

    public function setPhiKmAttribute($value)
    {
        $this->attributes['phi_km'] = $value;
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
    public function scopeHoatDong($query)
    {
        return $query->where('trang_thai', 'Hoạt động');
    }

    // Helper methods
    public function isActive()
    {
        return $this->trang_thai === 'Hoạt động';
    }
}