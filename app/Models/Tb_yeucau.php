<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helper\Traits\Filterable;

class Tb_yeucau extends Model
{
    use Filterable;
    protected $table = 'tb_yeucau';
    protected $fillable = [
        'MaGiayXN_Truong',
        'MaSinhVien',
        'NgayYeuCau',
        'NgayXuLy',
        'TrangThaiXuLy'
    ];
    public $timestamps = true;
}
