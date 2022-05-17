<?php

namespace App\Imports;

use App\Helper\Helper;
use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use App\Models\Tb_trangthai;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;

use Maatwebsite\Excel\Concerns\ToModel;

class UpdateUserImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;
    public $Err = [];
    protected $rowNum = 1;
    public $request;
    function __construct($request) {
        $this->request = $request;
      }
    public function model(array $row)
    {
        if (empty($row['tt'])) {
            return null;
        }
        ++$this->rowNum;
        $user = Tb_sinhvien::find($row['ma_sinh_vien']);
        if(!$user)
        {
            $error = ['err' => "Sinh viên này không quản lý hoặc nhập sai!", "row" => $this->rowNum];
            $this->Err[] = $error;
            return null;
        }
        $check =Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
        ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')->where("TenKhoa", "like", '%'.$this->request["Khoa"].'%')->exists();
        if (!$check) {
            $error = ['err' => "Sinh viên này không thuộc khoa đã chọn!", "row" => $this->rowNum];
            $this->Err[] = $error;
            return null;
        }
        
        if(!Str::contains(str::upper($user->TinhTrangSinhVien), Str::upper("Đang học")))
        {
            $error = ['err' => "Sinh viên hiện tại không còn quản lý", "row" => $this->rowNum];
            $this->Err[] = $error;
            return null;
        }
        if($this->request['SoQuyetDinh'] && !Helper::CheckDate($this->request['NgayQuyetDinh']))
        {
            $error = ['err' => "Ngày quyết định không đúng định dạng", "row" => $this->rowNum];
            $this->Err[] = $error;
            return null;
        }
        Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->update([
            'TinhTrangSinhVien' => $this->request["TinhTrangSinhVien"],
        ]);
        
        if(!Str::contains(str::upper($this->request["TinhTrangSinhVien"]), Str::upper("Đang học")))
            {
                Tb_trangthai::Create(['MaSinhVien'=>$row["ma_sinh_vien"], "NgayQuyetDinh"=>$this->request['NgayQuyetDinh'], "SoQuyetDinh"=>$this->request['SoQuyetDinh']]);
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
        ];
    }
    public function customValidationMessages()
    {
        return [  
            'ma_sinh_vien.required' => 'Cột mã sinh viên là bắt buộc',
        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }
}
