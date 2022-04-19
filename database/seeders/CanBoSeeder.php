<?php

namespace Database\Seeders;

use App\Models\Tb_canbo;
use Illuminate\Database\Seeder;

class CanBoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Tb_canbo::insert([
            'HoVaTen'=>"Nguyễn Duy Việt",
            'TrangThai'=>"Đang hoạt động",
            'ChucVu'=>"Chỉ huy trưởng",
            'ThoiGianKetThuc'=>'2022-1-1',
            'ThoiGianBatDau'=>"2019-1-1"
        ]);
    }
}
