<?php

namespace App\Http\Controllers;
use App\Exports\MoveMilitaryExport;
use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_giay_dc_truong;
use App\Models\Tb_sinhvien;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class MoveMilitaryController extends Controller
{
    public function Move(Request $request){ //MaGiayDK, SoGioiThieuDC, NoiOHienTai, NoiChuyenVe, LyDoChuyen, NgayHetHan
        if(Tb_giay_cn_dangky::join('Tb_sinhvien', 'Tb_sinhvien.MaSinhVien', '=', 'Tb_giay_cn_dangky.MaSinhVien')
            ->where('MaGiayDK', '=', $request->MaGiayDK)
            ->where(function ($query) {
                $query->where('TinhTrangSinhVien', '=', 'Đã tốt nghiệp')
                    ->orWhere('TinhTrangSinhVien', '=', 'Đã thôi học');
        })->exists()){
            $move = Tb_sinhvien::join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
                                ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_sinhvien.TinhTrangSinhVien', 'Tb_giay_cn_dangky.SoDangKy', 'Tb_giay_cn_dangky.NgayDangKy', 'Tb_giay_cn_dangky.NoiDangKy')
                                ->get();

            Tb_giay_dc_truong::insert([
                'SoGioiThieuDC' => $request->SoGioiThieuDC,
                'NgayCap'       => Carbon::now()->toDateString(), 
                'NgayHH'        => $request->NgayHH,
                'NoiChuyenVe'   => $request->NoiChuyenVe,
                'NoiOHienTai'   => $request->NoiOHienTai,
                'LyDo'          => $move[0]["TinhTrangSinhVien"],
                'MaGiayDK'      => $request->MaGiayDK,
            ]);

            ///NgayCap
            $NgayCap = Carbon::now()->toDateString();
            $NgayCap =  explode("-", $NgayCap);

            ///NgaySinh
            $NgaySinh = explode("-", $move[0]["NgaySinh"]);
            //Ngay het han
            $NgayHH = explode("-", $request->NgayHH);
            //Ngay Dang ky
            $NgayDangKy = explode("-", $move[0]["NgayDangKy"]);
            $NgayDangKy = $NgayDangKy[2][0].$NgayDangKy[2][1] . '/' . $NgayDangKy[1] . '/' . $NgayDangKy[0];

            $templateProcessor = new TemplateProcessor('TemplateMilitary/MoveMilitaryTemplate.docx');
            $templateProcessor->setValue('HoTen', $move[0]["HoTen"]);
            $templateProcessor->setValue('SoGioiThieuDC', $request->SoGioiThieuDC);
            $templateProcessor->setValue('SoDangKy', $move[0]["SoDangKy"]);
            $templateProcessor->setValue('NoiDangKy', $move[0]["NoiDangKy"]);
            $templateProcessor->setValue('NgayDangKy', $NgayDangKy);
            $templateProcessor->setValue('Ngay', $NgayCap[2]);
            $templateProcessor->setValue('Thang', $NgayCap[1]);
            $templateProcessor->setValue('Nam', $NgayCap[0]);
            $templateProcessor->setValue('NgaySinh', $NgaySinh[2][0].$NgaySinh[2][1]);
            $templateProcessor->setValue('ThangSinh', $NgaySinh[1]);
            $templateProcessor->setValue('NamSinh', $NgaySinh[0]);
            $templateProcessor->setValue('NoiOHienTai', $request->NoiOHienTai);
            $templateProcessor->setValue('NoiChuyenVe', $request->NoiChuyenVe);
            $templateProcessor->setValue('LyDo', $move[0]["TinhTrangSinhVien"]);
            $templateProcessor->setValue('NgayHH', $NgayHH[2][0].$NgayHH[2][1]);
            $templateProcessor->setValue('ThangHH', $NgayHH[1]);
            $templateProcessor->setValue('NamHH', $NgayHH[0]);

            $filename = $move[0]["HoTen"] . '_' . $move[0]["MaSinhVien"] . '_GiayDiChuyenNVQS';
            $templateProcessor->saveAs($filename . '.docx');
            return response()->download($filename . '.docx')->deleteFileAfterSend(true);

        }
        else{
            return response()->json(['status' => "Failed", 'err_message' => "Mã giấy chứng nhận đăng ký nghĩa vụ quân sự không tồn tại hoặc sinh viên đang học!!!"]);
        }
    }
}
