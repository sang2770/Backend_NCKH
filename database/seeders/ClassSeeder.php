<?php

namespace Database\Seeders;

use App\Models\Tb_lop;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_lop::insert([
            'MaLop' => '1',
            'TenLop' => 'CNTT1',
            'Khoas' => 'K60',
            'MaKhoa' => '1'
        ]);
        Tb_lop::insert([
            'MaLop' => '2',
            'TenLop' => 'CNTT2',
            'Khoas' => 'K60',
            'MaKhoa' => '1'
        ]);
        Tb_lop::insert([
            'MaLop' => '3',
            'TenLop' => 'CNTT3',
            'Khoas' => 'K60',
            'MaKhoa' => '1'
        ]);
        Tb_lop::insert([
            'MaLop' => '4',
            'TenLop' => 'CK1',
            'Khoas' => 'K62',
            'MaKhoa' => '2'
        ]);
    }
}
