<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_lichsu extends Model
{

    protected $table = 'tb_lichsu';
    protected $primaryKey = 'ID';
    protected $fillable = [
        'ID',
        'NoiDung',
        'MaSinhVien',
        'MaTK',
    ];
    public $timestamps = true;
    const CREATED_AT = "ThoiGian";
    const UPDATED_AT = null;
}
