<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHangChiTiet extends Model
{
    use HasFactory;

    protected $table = 'gio_hang_chi_tiet';
    protected $primaryKey = 'MaChiTiet';
    public $timestamps = false;

    protected $fillable = [
        'MaGioHang',
        'MaMonAn',
        'SoLuong',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
    ];

    // Relationships
    public function gioHang()
    {
        return $this->belongsTo(GioHang::class, 'MaGioHang', 'MaGioHang');
    }

    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'MaMonAn', 'MaMonAn');
    }

    // Accessors
    public function getThanhTienAttribute()
    {
        return $this->SoLuong * ($this->monAn->Gia ?? 0);
    }
}