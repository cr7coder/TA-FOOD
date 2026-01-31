<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHangChiTiet extends Model
{
    use HasFactory;

    protected $table = 'don_hang_chi_tiet';
    protected $primaryKey = 'MaChiTiet';
    public $timestamps = false;

    protected $fillable = [
        'MaDonHang',
        'MaMonAn',
        'SoLuong',
        'Gia',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
        'Gia' => 'decimal:2',
    ];

    // Relationships
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'MaDonHang', 'MaDonHang');
    }

    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'MaMonAn', 'MaMonAn');
    }

    // Accessors
    public function getThanhTienAttribute()
    {
        return $this->SoLuong * $this->Gia;
    }

    public function getGiaFormatAttribute()
    {
        return number_format((float)$this->Gia, 0, ',', '.');
    }

    public function getThanhTienFormatAttribute()
    {
        return number_format((float)$this->getThanhTienAttribute(), 0, ',', '.');
    }
}