<?php

namespace Database\Seeders;

use App\Models\Tb_tk_quanly;
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
        Tb_tk_quanly::insert([
            'TenDangNhap' => 'military.utc@gmail.com',
            'MatKhau' => Hash::make('123456@utc')
        ]);
    }
}
