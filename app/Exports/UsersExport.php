<?php

namespace App\Exports;

use App\Models\Tb_sinhvien;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $input;
    public function __construct(Request $request)
    {
        $this->input = $request;
    }

    public function headings(): array
    {
        return [
            'Mã sinh viên',
            'Họ và tên',
            'Ngày sinh',
            'Nơi sinh',
            'Giới tính',
            'Dân tộc',
            'Tôn giáo',
            'Quốc tịch',
            'Địa chỉ báo tin',
            'Số điện thoại',
            'Email',
            'Hộ khẩu tỉnh',
            'Hộ khẩu huyện',
            'Hộ khẩu xã phường',
            'Tình trạng sinh viên',
            'Hệ đào tạo',
            'Tên khoa',
            'Tên lớp',
            'Số CMTND',
            'Ngày cấp CMTND',
            'Nơi cấp CMTND'
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $limit = $this->input->query('limit');
        $page = $this->input->query('page');
        $user = Tb_sinhvien::join('Tb_lop', 'Tb_sinhvien.MaLop', '=', 'Tb_lop.MaLop')
            ->join('Tb_khoa', 'Tb_lop.MaKhoa', '=', 'Tb_khoa.MaKhoa')
            ->filter($this->input)->get([
                'MaSinhVien', 'HoTen', 'NgaySinh', 'NoiSinh', 'GioiTinh', 'DanToc',
                'TonGiao', 'QuocTich', 'DiaChiBaoTin', 'SDT', 'Email', 'HoKhauTinh', 'HoKhauHuyen',
                'HoKhauXaPhuong', 'TinhTrangSinhVien', 'HeDaoTao', 'TenKhoa', 'TenLop', 'SoCMTND', 'NgayCapCMTND', 'NoiCapCMTND'
            ]);
        return $user;
    }
}
