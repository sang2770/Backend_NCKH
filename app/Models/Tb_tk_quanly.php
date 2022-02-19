<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Tb_tk_quanly extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'Tb_tk_quanly';
    protected $primaryKey = 'MaTK';

    protected $fillable = [
        'MaTK',
        'TenDangNhap',
        'MatKhau',
    ];
    protected $hidden = [
        'MatKhau',
        'remember_token',
    ];
    public function getAuthPassword()
    {
        // var_dump("Admin");
        return $this->MatKhau;
    }
    public function getEmailAttribute()
    {
        return $this->TenDangNhap;
    }
}
