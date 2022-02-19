<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_khoa extends Model
{
    protected $table = 'Tb_khoa';
    protected $fillable = [
        'MaKhoa',
        'TenKhoa',
        'DiaChi',
        'SoDienThoai',
    ];
    protected $primaryKey = 'MaKhoa';
    public $timestamps = false;
}
