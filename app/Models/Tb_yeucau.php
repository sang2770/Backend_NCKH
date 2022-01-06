<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_yeucau extends Model
{
    protected $table = 'tb_yeucau';
    protected $fillable = [
        'MaGiayXN_Truong', 
        'MaSinhVien', 
        'NgayYeuCau',
        'NgayXuLy'
    ];
    public $timestamps = true;
}
