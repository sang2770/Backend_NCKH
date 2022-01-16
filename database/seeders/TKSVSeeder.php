<?php

namespace Database\Seeders;

use App\Models\Tb_tk_sinhvien;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TKSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_tk_sinhvien::insert([
            'TenDangNhap' => "NguyenHoang",
            'MatKhau' =>  Hash::make('1234'),
            'MaSinhVien' => "19123311",
        ]);
        Tb_tk_sinhvien::insert([
            'TenDangNhap' => "NguyenSang",
            'MatKhau' => "12345",
            'MaSinhVien' => "191204227",
        ]);
        Tb_tk_sinhvien::insert([
            'TenDangNhap' => "NguyenNam",
            'MatKhau' => "12345",
            'MaSinhVien' => "191204226",
        ]);
    }
}
