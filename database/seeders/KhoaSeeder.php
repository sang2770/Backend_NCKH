<?php

namespace Database\Seeders;

use App\Models\Tb_khoa;
use Illuminate\Database\Seeder;

class KhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_khoa::insert([
            'MaKhoa' => '1',
            'TenKhoa' => 'CNTT',
            'DiaChi' => 'CauGiay-HaNoi',
            'SoDienThoai' => '0986871487'
        ]);
        Tb_khoa::insert([
            'MaKhoa' => '2',
            'TenKhoa' => 'CK',
            'DiaChi' => 'CauGiay-HaNoi',
            'SoDienThoai' => '0986871345'
        ]);
        Tb_khoa::insert([
            'MaKhoa' => '3',
            'TenKhoa' => 'QTKD',
            'DiaChi' => 'CauGiay-HaNoi',
            'SoDienThoai' => '0986468218'
        ]);
    }
}
