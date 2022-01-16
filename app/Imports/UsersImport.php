<?php

namespace App\Imports;

use App\Models\Tb_cmtnd;
use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helper\Helper;
use App\Models\Tb_tk_sinhvien;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use \PhpOffice\PhpSpreadsheet\Shared\Date;

class UsersImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $rowNum;
    public $Err = [];
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
        // Create Tài Khoản
        $TaiKhoan = Helper::CreateUsers(["MaSinhVien" => (string)$row["ma_sinh_vien"], "NgaySinh" => (string)$row["ngay_sinh"], "HoTen" => $row["ho_ten"]]);
        // var_dump($TaiKhoan);
        return [
            new Tb_sinhvien([
                'MaSinhVien' => $row['ma_sinh_vien'],
                'HoTen' => $row['ho_ten'],
                'NgaySinh' => Date::excelToDateTimeObject(intval($row['ngay_sinh']))->format('Y-m-d'),
                'NoiSinh' => $row['noi_sinh'],
                'GioiTinh' => $row['gioi_tinh'],
                'DanToc' => $row["dan_toc"],
                'TonGiao' => $row['ton_giao'],
                'QuocTich' => $row['quoc_tich'],
                'DiaChiBaoTin' => $row['dia_chi_khi_bao_tin'],
                'SDT' => $row['so_dien_thoai'],
                'Email' => $row['email'],
                'HoKhauTinh' => $row['ho_khau_tinhtp'],
                'HoKhauHuyen' => $row['ho_khau_quanhuyen'],
                'HoKhauXaPhuong' => $row['ho_khau_xaphuong'],
                'TinhTrangSinhVien' => $row['tinh_trang_sinh_vien'],
                'HeDaoTao' => $row['he_dao_tao'],
                'MaLop' => $MaLop,
                'SoCMTND' => $row['chung_minh_nhan_dan'],
                'NgayCapCMTND' => Date::excelToDateTimeObject(intval($row['ngay_cap_cmnd']))->format('Y-m-d'),
                'NoiCapCMTND' => $row['noi_cap_cmnd'],

            ]),
            new Tb_tk_sinhvien(
                $TaiKhoan
            )
        ];
    }
    public function  rules(): array
    {
        return [
            "*.ma_sinh_vien" => "required|unique:Tb_sinhvien,MaSinhVien",
            '*.ho_ten' => "required",
            "*.ngay_sinh" => "required",
            "*.noi_sinh" => "required",
            "*.email" => "required|email",
            "*.gioi_tinh" => "required",
            "*.ton_giao" => "required",
            "*.quoc_tich" => "required",
            "*.dan_toc" => "required",
            "*.so_dien_thoai" => "required|digits:10",
            "*.dia_chi_khi_bao_tin" => "required",
            "*.he_dao_tao" => "required",
            "*.tinh_trang_sinh_vien" => "required",
            "*.ho_khau_tinhtp" => "required",
            "*.ho_khau_quanhuyen" => "required",
            "*.ho_khau_xaphuong" => "required",
            "*.ten_lop" => "required",
            "*.chung_minh_nhan_dan" => "required|unique:Tb_sinhvien,SoCMTND",
            "*.ngay_cap_cmnd" => "required",
            "*.noi_cap_cmnd" => "required",
        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
