<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_thongbaochinh extends Model
{
    protected $table = 'tb_thongbaochinh';
    protected $fillable = [
        'MaThongBaoChinh', 
        'TieuDeTB',
        'NoiDungTB', 
        'ThoiGianTB',
    ];
    protected $primaryKey = 'MaThongBaoChinh';
    public $timestamps = true;
}
