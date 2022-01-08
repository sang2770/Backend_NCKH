<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    protected $primaryKey = 'MaSinhVien';
    public $timestamps = true;
}
