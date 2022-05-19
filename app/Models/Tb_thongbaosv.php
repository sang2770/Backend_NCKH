<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_thongbaosv extends Model
{
    protected $table = 'tb_thongbaosv';
    protected $fillable = [
        'ThoiGianTB',
        'MaTKSV',
        'MaThongBaoChinh',
    ];
    public $timestamps = true;
}
