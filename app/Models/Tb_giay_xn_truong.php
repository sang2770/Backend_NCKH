<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_giay_xn_truong extends Model
{
    protected $table = 'tb_giay_xn_truong';
    protected $fillable = [
        'MaGiayXN_Truong', 
        'NgayCap', 
        'NamHoc',
    ];
    protected $primaryKey = 'MaGiayXN_Truong';
    public $timestamps = true;
}
