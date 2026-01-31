<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    use HasFactory;

    protected $table = 'gio_hang';
    protected $primaryKey = 'MaGioHang';

    protected $fillable = [
        'MaNguoiDung',
        'session_id',
    ];

    // Relationships
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }

    public function chiTiet()
    {
        return $this->hasMany(GioHangChiTiet::class, 'MaGioHang', 'MaGioHang')
            ->with('monAn');
    }

    // Accessors
    public function getTongTienAttribute()
    {
        return $this->chiTiet->sum(function($ct) {
            return $ct->SoLuong * ($ct->monAn->Gia ?? 0);
        });
    }

    public function getTongSoLuongAttribute()
    {
        return $this->chiTiet->sum('SoLuong');
    }

    // Helper methods
    public function addItem($maMonAn, $soLuong = 1)
    {
        $existing = $this->chiTiet()->where('MaMonAn', $maMonAn)->first();

        if ($existing) {
            $existing->increment('SoLuong', $soLuong);
            return $existing;
        }

        return $this->chiTiet()->create([
            'MaMonAn' => $maMonAn,
            'SoLuong' => $soLuong,
        ]);
    }

    public function updateItem($maMonAn, $soLuong)
    {
        return $this->chiTiet()
            ->where('MaMonAn', $maMonAn)
            ->update(['SoLuong' => $soLuong]);
    }

    public function removeItem($maMonAn)
    {
        return $this->chiTiet()
            ->where('MaMonAn', $maMonAn)
            ->delete();
    }

    public function clear()
    {
        return $this->chiTiet()->delete();
    }
}