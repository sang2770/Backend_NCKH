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
        "NgayQuanLy",
        "NgayKetThuc"
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    protected $primaryKey = 'MaSinhVien';
    const CREATED_AT = 'NgayQuanLy';
    const UPDATED_AT = 'updated_date';
    public function filterNam($query, $value)
    {
        $NamHoc=explode('-',$value);
        $Pre=$NamHoc[0];
        $Next=$NamHoc[1];
        return $query->whereYear('NgayQuanLy', $Pre)
                    ->orWhereYear('NgayQuanLy', $Next)
                    ->orWhereYear('NgayKetThuc', $Pre)
                    ->orWhereYear('NgayKetThuc', $Next)
                    ->whereMonth('NgayQuanLy','>=', 8)
                    ->orWhereMonth('NgayQuanLy','<', 8)
                    ->whereMonth('NgayKetThuc','>=', 8)
                    ->whereMonth('NgayKetThuc','<', 8);
                    
    }
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
        return $query->where('TenKhoa',$value );
    }
    public function filterKhoas($query, $value)
    {
        return $query->where('Khoas', $value);
    }
    public function filterTinhtrangsinhvien($query, $value)
    {
        return $query->where('TinhTrangSinhVien',"like", '%' . $value . '%');
    }
    public function filterTrangThaiXuLy($query, $value)
    {
        return $query->where('TrangThaiXuLy', $value);
    }
    
    public function filterDangKy($query, $value)
    {
        $Giay=Tb_giay_cn_dangky::select("MaSinhVien")->get()->toArray();
        if(Str::upper($value)==Str::upper("Đã nộp"))
        {
            return $query->whereIn('MaSinhVien', $Giay);

        }else if(Str::upper($value)==Str::upper("Chưa nộp"))
        {
            return $query->whereNotIn('MaSinhVien', $Giay);;
        }else{
            return $query;
        }
    }
    public function routeNotificationForMail($notification)
    {
        return $this->Email;
    }
}
