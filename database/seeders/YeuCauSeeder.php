<?php

namespace Database\Seeders;

use App\Models\Tb_yeucau;
use Illuminate\Database\Seeder;

class YeuCauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_yeucau::insert([
            'MaSinhVien' => '191204229',
            'NgayYeuCau' => '2021-01-10',
            'NgayXuLy' => '2021-01-12',
            'TrangThaiXuLy' => 'Đã xử lý',
            'LanXinCap'     => '1'
        ]);
        Tb_yeucau::insert([
            'MaSinhVien' => '191204225',
            'NgayYeuCau' => '2021-10-10',
            'NgayXuLy' => '2021-10-12',
            'TrangThaiXuLy' => 'Đã xử lý',
            'LanXinCap'     => '1'
        ]);
        Tb_yeucau::insert([
            'MaSinhVien' => '191204226',
            'NgayYeuCau' => '2022-02-10',
            'NgayXuLy' => '2022-02-12',
            'TrangThaiXuLy' => 'Đã xử lý',
            'LanXinCap'     => '1'
        ]);
        Tb_yeucau::insert([
            'MaSinhVien' => '191204227',
            'NgayYeuCau' => '2022-01-10',
            'NgayXuLy' => '2022-01-12',
            'TrangThaiXuLy' => 'Đã xử lý',
            'LanXinCap'     => '1'
        ]);
        Tb_yeucau::insert([
            'MaSinhVien' => '191204228',
            'NgayYeuCau' => '2022-01-20',
            'NgayXuLy' => '2022-01-20',
            'TrangThaiXuLy' => 'Chờ xử lý',
            'LanXinCap'     => '2'
        ]);
        Tb_yeucau::insert([
            'MaSinhVien' => '191204229',
            'NgayYeuCau' => '2022-10-10',
            'NgayXuLy' => '2022-10-12',
            'TrangThaiXuLy' => 'Chờ xử lý',
            'LanXinCap'     => '1'
        ]);
    }
}
