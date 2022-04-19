<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_canbo extends Model
{
    use HasFactory;
    protected $table = 'tb_canbo';
    protected $fillable = [
       ' MaCanBo',
        'HoVaTen',
        'TrangThai',
        'ChucVu',
        'ThoiGianKetThuc',
        'ThoiGianBatDau'
    ];
    
    protected $primaryKey = 'MaCanBo';
    public $timestamps = false;
}
