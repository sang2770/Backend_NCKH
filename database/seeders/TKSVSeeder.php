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
            'TenDangNhap' => "1",
            'MatKhau' => "1",
            'MaSinhVien' => "1",
        ]);
    }
}
