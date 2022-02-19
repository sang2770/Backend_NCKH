<?php

namespace App\Models;

use App\Helper\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tb_sinhvien extends Authenticatable
{
    use Filterable, Notifiable;
    protected $table = 'Tb_sinhvien';
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
    public function filterLop($query, $value)
    {
        return $query->where('TenLop', 'LIKE', '%' . $value . '%');
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
    public function filterTrangThaiXuLy($query, $value)
    {
        return $query->where('TrangThaiXuLy', $value);
    }
    public function routeNotificationForMail($notification)
    {
        return $this->Email;
    }
}
