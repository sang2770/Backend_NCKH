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
        
        return $query->whereYear('NgayQuanLy', $value)
                    ->orWhereYear('NgayKetThuc', $value);
                    
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
    public function filterNgaySinh($query, $value)
    {
        $date=explode("-", $value);
        if(count($date)<3)
        {
            return $query;
        }
        return $query->where('NgaySinh', date($value));
    }
    
    public function filterDangKy($query, $value)
    {
        $Giay=Tb_giay_cn_dangky::select("MaSinhVien")->get()->toArray();
        if(Str::upper($value)==Str::upper("???? n???p"))
        {
            return $query->whereIn('Tb_sinhvien.MaSinhVien', $Giay);

        }else if(Str::upper($value)==Str::upper("Ch??a n???p"))
        {
            return $query->whereNotIn('Tb_sinhvien.MaSinhVien', $Giay);;
        }else{
            return $query;
        }
    }
    public function filterXacNhan($query, $value)
    {
        $Giay=Tb_giay_dc_diaphuong::select("MaSinhVien")->get()->toArray();
        if(Str::upper($value)==Str::upper("???? n???p"))
        {
            return $query->whereIn('tb_sinhvien.MaSinhVien', $Giay);

        }else if(Str::upper($value)==Str::upper("Ch??a n???p"))
        {
            return $query->whereNotIn('tb_sinhvien.MaSinhVien', $Giay);;
        }else{
            return $query;
        }
    }
    
    public function routeNotificationForMail($notification)
    {
        return $this->Email;
    }
}
