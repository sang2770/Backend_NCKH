<?php

namespace App\Models;

use App\Helper\Traits\Filter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filterable;

class Tb_sinhvien extends Model
{
    protected $table = 'tb_sinhvien';
    protected $fillable = [
        'MaSinhVien',
        'HoTen',
        'NgaySinh',
        'NoiSinh',
        'GioiTinh',
        'DanToc',
        'TonGiao',
        'QuocTich',
        'SoCMTND',
        'NgayCapCMTND',
        'NoiCapCMTND',
        'DiaChiBaoTin',
        'SDT',
        'Email',
        'HoKhauTinh',
        'HoKhauHuyen',
        'HoKhauXaPhuong',
        'TinhTrangSinhVien',
        'HeDaoTao',
        'MaLop',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    protected $primaryKey = 'MaSinhVien';
    public $timestamps = true;

    public function filterMasinhvien($query, $value)
    {
        return $query->where('MaSinhVien', $value);
    }
    public function filterHoten($query, $value)
    {
        return $query->where('HoTen', 'LIKE', '%' . $value . '%');
    }
    public function filterKhoa($query, $value)
    {
        return $query->where('TenKhoa', 'LIKE', '%' . $value . '%');
    }
    public function filterKhoas($query, $value)
    {
        return $query->where('Khoas', $value);
    }
    public function filterTinhtrangsinhvien($query, $value)
    {
        return $query->where('TinhTrangSinhVien', $value);
    }
}
