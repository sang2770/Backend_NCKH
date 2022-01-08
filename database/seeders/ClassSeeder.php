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
            'Khoa' => '60',
            'MaKhoa' => '1'
        ]);
    }
}
