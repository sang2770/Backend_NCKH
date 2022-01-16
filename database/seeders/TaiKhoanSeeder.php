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
            'TenDangNhap' => 'QLNVQS_1@st.em.utc.edu.vn',
            'MatKhau' => Hash::make('1234')
        ]);
    }
}
