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
            'TenDangNhap' => "NguyenSang",
            'MatKhau' =>  Hash::make('1234'),
            'MaSinhVien' => "19123311",
        ]);
    }
}
