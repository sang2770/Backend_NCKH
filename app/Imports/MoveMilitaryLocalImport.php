<?php

namespace App\Imports;

use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_giay_dc_diaphuong;
use App\Models\Tb_sinhvien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use \PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class MoveMilitaryLocalImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public $Err = [];
    public function headingRow() : int
    {
        return 1;
    }
    public function model(array $row)
    {
        $madk = Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->value('MaSinhVien');
        if (!$madk) {
            $error = ['err' => ["Mã sinh viên này không tồn tại!"], "row" => $row['ma_sinh_vien']];
            $this->Err[] = $error;
            return null;
        }
        if(Tb_giay_dc_diaphuong::where('MaSinhVien', $madk)->doesntExist() 
        && Tb_sinhvien::where('MaSinhVien', $madk)->exists()){
            return new Tb_giay_dc_diaphuong([
                'SoGioiThieu'   => $row['so_gioi_thieu'], 
                'NgayCap'       => Date::excelToDateTimeObject($row['ngay_cap'])->format('Y-m-d'), 
                'NoiOHienTai'   => $row['noi_o_hien_tai'],
                'NoiChuyenDen'  => $row['noi_chuyen_den'],
                'LyDo'          => $row['ly_do'],
                'BanChiHuy'     => $row['ban_chi_huy'],
                'MaSinhVien'    => $row['ma_sinh_vien']
            ]);
        }
    }
    public function  rules(): array
    {
        return [
            "*.so_gioi_thieu"   => "required",
            '*.ngay_cap'        => "required",
            "*.noi_o_hien_tai"  => "required",
            "*.noi_chuyen_den"  => "required",
            "*.ly_do"           => "required",
            "*.ban_chi_huy"     => "required",
            "*.ma_sinh_vien"    => "required",
        ];
    }
    
    public function chunkSize(): int
    {
        return 500;
    }
}
