<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_trangthai extends Model
{
    protected $table = 'tb_trangthai';
    protected $fillable = [
        'SoQuyetDinh', 
        'NgayQuyetDinh', 
        'MaSinhVien',
    ];
    protected $primaryKey = 'MaSinhVien';
    public $timestamps = false;
}
