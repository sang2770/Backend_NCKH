<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_cmtnd extends Model
{
    protected $table = 'tb_cmtnd';
    protected $fillable = [
        'SoCMTND', 
        'NoiCap_CMTND', 
        'NgayCap_CMTND',
        'MaSinhVien'
    ];
    protected $primaryKey = 'SoCMTND';
    public $timestamps = false;

}
