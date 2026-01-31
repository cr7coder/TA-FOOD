<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MonAn extends Model
{
    use HasFactory;

    protected $table = 'mon_an';
    protected $primaryKey = 'MaMonAn';
    public $timestamps = true;

    protected $fillable = [
        'MaNhaHang',
        'TenMonAn',
        'DanhMuc',
        'MoTa',
        'Gia',
        'HinhAnh',
        'TrangThai',
    ];

    protected $casts = [
        'Gia' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        // Tự động gán MaNhaHang cho seller
        static::creating(function (MonAn $mon) {
            if (!$mon->MaNhaHang && Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                if ($user && $user->isSeller()) {
                    $maNhaHang = NhaHang::where('MaNguoiDung', $user->MaNguoiDung)
                        ->value('MaNhaHang');
                    if ($maNhaHang) {
                        $mon->MaNhaHang = $maNhaHang;
                    }
                }
            }

            // Mặc định trạng thái
            if (!$mon->TrangThai) {
                $mon->TrangThai = 'Còn bán';
            }
        });
    }

    // Relationships
    public function nhaHang()
    {
        return $this->belongsTo(NhaHang::class, 'MaNhaHang', 'MaNhaHang');
    }

    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'ma_mon_an', 'MaMonAn')
            ->where('trang_thai', 'Đã duyệt')
            ->orderBy('created_at', 'desc');
    }

    public function donHangChiTiet()
    {
        return $this->hasMany(DonHangChiTiet::class, 'MaMonAn', 'MaMonAn');
    }

    // Accessors
    public function getDiemTrungBinhAttribute()
    {
        $avg = $this->binhLuans()->avg('diem_danh_gia');
        return $avg ? round($avg, 1) : 0;
    }

    public function getTongBinhLuanAttribute()
    {
        return $this->binhLuans()->count();
    }

    public function getGiaFormatAttribute()
    {
        return number_format((float)$this->Gia, 0, ',', '.');
    }

    public function getLoaiMonClassAttribute()
    {
        $map = [
            'Cơm' => 'com',
            'Bún' => 'bun',
            'Phở' => 'pho',
            'Mì'  => 'mi',
            'Trà' => 'tra',
        ];

        $value = $this->DanhMuc ?? '';
        return $map[$value] ?? Str::slug($value, '-');
    }

    // Scopes
    public function scopeConBan($query)
    {
        return $query->where('TrangThai', 'Còn bán');
    }

    public function scopeOfSeller($query, int $sellerId)
    {
        return $query->whereHas('nhaHang', function ($q) use ($sellerId) {
            $q->where('MaNguoiDung', $sellerId);
        });
    }

    public function scopeDanhMuc($query, string $danhMuc)
    {
        return $query->where('DanhMuc', $danhMuc);
    }

    // Helper methods
    public function isBan()
    {
        return $this->TrangThai === 'Còn bán';
    }
}