<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_giay_dc_diaphuong extends Model
{
    protected $table = 'tb_giay_dc_diaphuong';
    protected $fillable = [
        'SoGioiThieu', 
        'NgayCap', 
        'NgayHH',
        'NoiOHienTai',
        'NoiChuyenDen',
        'LyDo',
        'SoDangKy',
    ];
    protected $primaryKey = 'SoGioiThieu';
    public $timestamps = true;
}
