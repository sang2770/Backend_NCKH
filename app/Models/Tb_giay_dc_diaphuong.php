<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_giay_dc_diaphuong extends Model
{
    protected $table = 'Tb_giay_dc_diaphuong';
    protected $fillable = [
        'MaGiayDC_DP',
        'SoGioiThieu', 
        'NgayCap', 
        'NgayHH',
        'NoiOHienTai',
        'NoiChuyenDen',
        'LyDo',
        'BanChiHuy',
        'MaGiayDK',
    ];
    protected $primaryKey = 'MaGiayDC_DP';
    public $timestamps = true;
}
