<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_tk_sinhvien extends Model
{
    protected $table = 'tb_tk_sinhvien';
    protected $fillable = [
        'MaTKSV', 
        'TenDangNhap', 
        'MatKhau',
        'MaSinhVien',
    ];
    protected $primaryKey = 'MaTKSV';
    public $timestamps = true;
}
