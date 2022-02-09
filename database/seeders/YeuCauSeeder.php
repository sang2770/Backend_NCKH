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
            'MaSinhVien' => '211200574',
            'NgayYeuCau' => '2022-01-20',
            'NgayXuLy' => '2022-01-20',
            'TrangThaiXuLy' => 'Chờ xử lý'
        ]);
        Tb_yeucau::insert([
            'MaSinhVien' => '211200574',
            'NgayYeuCau' => '2022-01-10',
            'NgayXuLy' => '2022-01-12',
            'TrangThaiXuLy' => 'Đã xử lý'
        ]);
    }
}
