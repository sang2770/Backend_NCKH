<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Tb_tk_sinhvien extends Authenticatable
{
        use HasApiTokens, HasFactory, Notifiable;

        protected $table = 'tb_tk_sinhvien';
        protected $fillable = [
                'MaTKSV',
                'MatKhau',
                'MaSinhVien',
        ];
        protected $hidden = [
                'MatKhau',
        ];
        protected $primaryKey = 'MaTKSV';
        public $timestamps = true;
        public function getAuthPassword()
        {
                return $this->MatKhau;
        }
        public function getEmailAttribute()
        {
                return $this->MaSinhVien;
        }
        
}
