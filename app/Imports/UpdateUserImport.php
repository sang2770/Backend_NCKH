<?php

namespace App\Imports;

use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;

class UpdateUserImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;
    public $Err = [];
    protected $rowNum = 1;
    public function model(array $row)
    {
        if (empty($row['tt'])) {
            return null;
        }
        ++$this->rowNum;
        $TenLop = $row['ten_lop'];
        $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
        if (!$MaLop) {
            $error = ['err' => "Không tồn tại Tên lớp!", "row" => $this->rowNum];
            $this->Err[] = $error;
            return null;
        }
        Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->update([
            'TinhTrangSinhVien' => $row['tinh_trang_sinh_vien'],
        ]);
        if(!Str::contains(str::upper($row['tinh_trang_sinh_vien']), Str::upper("Đang học")))
            {
                Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->update(["NgayKetThuc"=>date('Y-m-d')]);
            }else{
                Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->update(["NgayKetThuc"=>null]);     
            }
        return null;
    }
    public function  rules(): array
    {
        return [
            "*.ma_sinh_vien" => "required",
            '*.ho_ten' => "required",
            "*.tinh_trang_sinh_vien" => "required",
            "*.ten_lop" => "required"
        ];
    }
    public function customValidationMessages()
    {
        return [
            
            'ma_sinh_vien.required' => 'Cột mã sinh viên là bắt buộc',
            'ho_ten.required' => 'Cột họ tên là bắt buộc',
            'tinh_trang_sinh_vien.required'=>'Cột tình trạng sinh viên là bắt buộc là bắt buộc'

        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
