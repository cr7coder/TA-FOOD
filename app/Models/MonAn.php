<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\SoftDeletes;

class MonAn extends Model
{
    use HasFactory, SoftDeletes;

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
        'ThuVienAnh',
        'TrangThai',
    ];

    protected $casts = [
        'Gia' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'diem_trung_binh',
        'tong_binh_luan',
        'gia_format',
        'loai_mon_class',
        'is_category_active',
        'is_favorite',
        'hinh_anh_url',
        'thu_vien_anh_urls'
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
        return $this->hasMany(BinhLuan::class, 'MaMonAn', 'MaMonAn')
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

    public function getIsCategoryActiveAttribute()
    {
        if (empty($this->DanhMuc)) {
            return false;
        }
        return \App\Models\DanhMuc::where('TenDanhMuc', $this->DanhMuc)
            ->where('TrangThai', 'Hoạt động')
            ->exists();
    }

    public function getHinhAnhUrlAttribute()
    {
        if ($this->HinhAnh) {
            if (filter_var($this->HinhAnh, FILTER_VALIDATE_URL)) {
                return $this->HinhAnh; // Cloudinary / external URL — giữ nguyên
            }
            return '/images/' . $this->HinhAnh; // local file — dùng path tương đối
        }
        return '/images/no-image.png';
    }

    public function getThuVienAnhUrlsAttribute()
    {
        if ($this->ThuVienAnh) {
            $images = json_decode($this->ThuVienAnh, true);
            if (is_array($images)) {
                return array_map(function($img) {
                    return filter_var($img, FILTER_VALIDATE_URL) ? $img : '/images/' . $img;
                }, $images);
            }
        }
        return [];
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

    public function yeuThichNguoiDungs()
    {
        return $this->belongsToMany(User::class, 'mon_an_yeu_thich', 'MaMonAn', 'MaNguoiDung')
            ->withTimestamps();
    }

    public function getIsFavoriteAttribute()
    {
        if (Auth::check()) {
            return $this->yeuThichNguoiDungs()->where('mon_an_yeu_thich.MaNguoiDung', Auth::id())->exists();
        }
        return false;
    }
}