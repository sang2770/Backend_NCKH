<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class TaiKhoanQuanLy extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "tk";
    protected $primaryKey = "Id";
    public $timestamps = false;

    protected $fillable = [
        'id',
        'TenDangNhap',
        'MatKhau'
    ];

    protected $hidden = [
        'MatKhau',
        'remember_token',
    ];
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}
