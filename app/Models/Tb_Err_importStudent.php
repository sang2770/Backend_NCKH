<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Err_importStudent extends Model
{
    use HasFactory;
    protected $table = 'tb_errimportstudent';
    protected $fillable = [
        'NoiDung',
        'ThoiGian',
        'MaTK',
        'TrangThai'
    ];
    const CREATED_AT = "ThoiGian";
    const UPDATED_AT = null;
}
