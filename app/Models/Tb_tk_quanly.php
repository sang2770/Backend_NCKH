<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_tk_quanly extends Model
{
    protected $table = 'tb_tk_quanly';
    protected $fillable = [
        'MaTK', 
        'TenDangNhap', 
        'MatKhau',
    ];
    protected $primaryKey = 'MaTK';
    public $timestamps = true;
}

