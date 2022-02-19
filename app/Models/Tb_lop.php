<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_lop extends Model
{
    protected $table = 'Tb_lop';
    protected $fillable = [
        'MaLop', 
        'TenLop', 
        'Khoas',
        'MaKhoa',
    ];
    protected $primaryKey = 'MaLop';
    public $timestamps = false;
}
