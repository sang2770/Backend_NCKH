<?php

namespace Database\Seeders;

use App\Models\TaiKhoanQuanLy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaiKhoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaiKhoanQuanLy::insert([
            'Id' => 1,
            'TenDangNhap' => 'NguyenSang',
            'MatKhau' => Hash::make('1234')
        ]);
    }
}
