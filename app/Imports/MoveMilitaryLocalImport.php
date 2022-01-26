<?php

namespace App\Imports;

use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_giay_dc_diaphuong;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class MoveMilitaryLocalImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function headingRow() : int
    {
        return 1;
    }
    public function model(array $row)
    {
        if(Tb_giay_cn_dangky::where('MaGiayDK', $row['ma_giay_dang_ky'])->doesntExist()){
            echo "không tồn tại mã giấy đăng ký: ".$row['ma_giay_dang_ky']."\n";
        }
        if(Tb_giay_dc_diaphuong::where('MaGiayDK', $row['ma_giay_dang_ky'])->doesntExist() 
        && Tb_giay_cn_dangky::where('MaGiayDK', $row['ma_giay_dang_ky'])->exists()){
            return new Tb_giay_dc_diaphuong([
                'SoGioiThieu'   => $row['so_gioi_thieu'], 
                'NgayCap'       => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_cap'])->format('Y-m-d'), 
                'NgayHH'        => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_het_han'])->format('Y-m-d'),
                'NoiOHienTai'   => $row['noi_o_hien_tai'],
                'NoiChuyenDen'  => $row['noi_chuyen_den'],
                'LyDo'          => $row['ly_do'],
                'BanChiHuy'     => $row['ban_chi_huy'],
                'MaGiayDK'      => $row['ma_giay_dang_ky'],
            ]);
        }
    }
    public function  rules(): array
    {
        return [
            "*.so_gioi_thieu"   => "required",
            '*.ngay_cap'        => "required",
            "*.ngay_het_han"    => "required",
            "*.noi_o_hien_tai"  => "required",
            "*.noi_chuyen_den"  => "required",
            "*.ly_do"           => "required",
            "*.ban_chi_huy"     => "required",
            "*.ma_giay_dang_ky" => "required",
        ];
    }
    
    public function chunkSize(): int
    {
        return 500;
    }
}
