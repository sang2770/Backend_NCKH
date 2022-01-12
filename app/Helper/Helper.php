<?php

namespace App\Helper;

class Helper
{
    public static function CreateUsers($Input)
    {
        // var_dump($Input);
        try {
            $name = explode(" ", $Input['HoTen']);
            $name = $name[count($name) - 1];
            $NgaySinh =  explode("-", $Input['NgaySinh']);
            $NgaySinh = $NgaySinh[2] . "/" . $NgaySinh[1] . "/" . $NgaySinh[0];
            return [
                'TenDangNhap' => $name . $Input["MaSinhVien"] . "@st.utc.edu.vn",
                'MatKhau' => $NgaySinh,
                'MaSinhVien' => $Input["MaSinhVien"],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
