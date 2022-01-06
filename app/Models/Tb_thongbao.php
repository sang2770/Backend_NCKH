<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_thongbao extends Model
{
    protected $table = 'tb_thongbao';
    protected $fillable = [
        'MaThongBao', 
        'NoiDung', 
        'ThoiGianTB',
        'MaTKSV',
    ];
    protected $primaryKey = 'MaThongBao';
    public $timestamps = true;
}
