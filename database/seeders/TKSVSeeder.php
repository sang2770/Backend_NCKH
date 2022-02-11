<?php

namespace Database\Seeders;

use App\Models\Tb_tk_sinhvien;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TKSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tb_tk_sinhvien::insert([
        //     'TenDangNhap' => "quan19123311@st.utc.edu.vn",
        //     'MatKhau' =>  Hash::make('1234'),
        //     'MaSinhVien' => "19123311",
        // ]);
        Tb_tk_sinhvien::insert([
            'TenDangNhap' => "Sang@st.utc.edu.vn",
            'MatKhau' => Hash::make('1234'),
            'MaSinhVien' => "191233112",
        ]);

        // Tb_tk_sinhvien::insert([
        //     'TenDangNhap' => "sang191204227@st.utc.edu.vn",
        //     'MatKhau' => Hash::make('171372'),
        //     'MaSinhVien' => "191204227",
        // ]);

        // Tb_tk_sinhvien::insert([
        //     'TenDangNhap' => "hoang191204227@st.utc.edu.vn",
        //     'MatKhau' => Hash::make('34352'),
        //     'MaSinhVien' => "191204228",
        // ]);
    }
}
