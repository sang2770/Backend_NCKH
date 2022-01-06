<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_giay_cn_dangky extends Model
{
    protected $table = 'tb_giay_cn_dangky';
    protected $fillable = [
        'SoDangKy', 
        'NgayDangKy', 
        'NoiDangKy',
        'DiaChiThuongTru',
        'NgayNop',
        'MaGiayDC_Truong',
    ];
    protected $primaryKey = 'SoDangKy';
    public $timestamps = true;
}
