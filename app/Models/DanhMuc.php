<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanhMuc extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, SoftDeletes;

    protected $table = 'danh_mucs';
    protected $primaryKey = 'MaDanhMuc';
    public $timestamps = true;

    protected $fillable = [
        'TenDanhMuc',
        'Slug',
        'MoTa',
        'HinhAnh',
        'TrangThai',
    ];

    protected static function booted()
    {
        static::creating(function (DanhMuc $dm) {
            if (empty($dm->Slug)) {
                $dm->Slug = \Illuminate\Support\Str::slug($dm->TenDanhMuc, '-');
            }
        });

        static::updating(function (DanhMuc $dm) {
            if (empty($dm->Slug) || $dm->isDirty('TenDanhMuc')) {
                $dm->Slug = \Illuminate\Support\Str::slug($dm->TenDanhMuc, '-');
            }
        });
    }

    public function monAn()
    {
        return $this->hasMany(MonAn::class, 'DanhMuc', 'TenDanhMuc');
    }
}
