<?php

namespace App\Imports;

use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_sinhvien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use \PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class RegisterMilitaryImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation, SkipsOnFailure
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
        $msv = Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->value('MaSinhVien');
        if (!$msv) {
            $error = ['err' => ["Mã sinh viên này không tồn tại!"], "row" => $row['ma_sinh_vien']];
            $this->Err[] = $error;
            return null;
        }
        if(Tb_giay_cn_dangky::where('MaSinhVien', $row['ma_sinh_vien'])->doesntExist() 
        && Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->exists()){
            return new Tb_giay_cn_dangky([
                'SoDangKy'          => $row['so_dang_ky'],
                'NgayDangKy'        => Date::excelToDateTimeObject($row['ngay_dang_ky'])->format('Y-m-d'),
                'NoiDangKy'         => $row['noi_dang_ky'],
                'DiaChiThuongTru'   => $row['dia_chi_thuong_tru'],
                'NgayNop'           => Date::excelToDateTimeObject($row['ngay_nop'])->format('Y-m-d'),
                'MaSinhVien'        => $row['ma_sinh_vien'],
            ]);
        }
    }

    public function  rules(): array
    {
        return [
            "*.so_dang_ky"          => "required",
            '*.ngay_dang_ky'        => "required",
            "*.noi_dang_ky"         => "required",
            "*.dia_chi_thuong_tru"  => "required",
            "*.ngay_nop"            => "required",
            "*.ma_sinh_vien"        => "required|unique:tb_giay_cn_dangky,MaSinhVien",
        ];
    }
    
    public function chunkSize(): int
    {
        return 500;
    }
}
