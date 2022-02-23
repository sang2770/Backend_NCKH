<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_giay_dc_truong extends Model
{
    protected $table = 'Tb_giay_dc_truong';
    protected $fillable = [
        'MaGiayDC_Truong', 
        'SoGioiThieuDC',
        'NgayCap', 
        'NgayHH',
        'NoiChuyenVe',
        'NoiOHienTai',
        'LyDo',
        'MaGiayDK'
    ];
    protected $primaryKey = 'MaGiayDC_Truong';
    public $timestamps = true;
}
