<?php

namespace App\Helper;

use Illuminate\Support\Facades\Hash;

class Helper
{
    static function FormatText($str): string
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return strtolower($str);
    }
    public static function CreateUsers($Input)
    {
        // var_dump($Input);
        try {
            // $name = explode(" ", $Input['HoTen']);
            // $name = $name[count($name) - 1];
            // $name = self::FormatText($name);
            $NgaySinh =  explode("/", $Input['NgaySinh']);
            if (count($NgaySinh) == 1) {
                $NgaySinh =  explode("-", $NgaySinh[0]);
            }
            $NgaySinh = $NgaySinh[2] . "/" . $NgaySinh[1] . "/" . $NgaySinh[0];
            return [
                'MatKhau' => Hash::make($NgaySinh),
                'MaSinhVien' => $Input["MaSinhVien"],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public static  function CheckDate($date)
    {
        $NgaySinh =  explode("/", $date);
        if (count($NgaySinh) == 1) {
            $NgaySinh =  explode("-", $NgaySinh[0]);
        }
        if (count($NgaySinh) < 2 || count($NgaySinh) > 3) {
            return false;
        }
        // var_dump($NgaySinh);
        $day = $NgaySinh[2];
        $month = $NgaySinh[1];
        $year = $NgaySinh[0];
        if (checkdate($month, $day, $year)) {
            return true;
        } else {
            return false;
        }
    }
}
