<?php

namespace Database\Seeders;

use App\Models\Tb_tk_sinhvien;
use Illuminate\Database\Seeder;

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
            'MatKhau' => "12223",
            'MaSinhVien' => "191204228",
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
