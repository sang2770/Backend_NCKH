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
            'MaSinhVien' => '19123311',
            'NgayYeuCau' => '2022-1-1',
            'TrangThaiXuLy' => 'Chờ xử lý'
        ]);
    }
}
