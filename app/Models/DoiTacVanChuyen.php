<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoiTacVanChuyen extends Model
{
    use HasFactory;

    protected $table = 'doi_tac_van_chuyens';

    protected $fillable = [
        'ten_doi_tac',
        'so_dien_thoai',
        'dia_chi_tru_so',
        'email_lien_he',
        'phi_van_chuyen',
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