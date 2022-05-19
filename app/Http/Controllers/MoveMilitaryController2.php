<?php

namespace App\Http\Controllers;
use App\Models\Tb_sinhvien;
use App\Models\Tb_giay_dc_truong;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Nette\Utils\ArrayList;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;
use PhpOffice\PhpWord\TemplateProcessor;

class MoveMilitaryController2 extends Controller
{
    public function Moves(Request $request){
        $array = array();
        $templateProcessor = new TemplateProcessor('TemplateMilitary/MoveMilitaryTemplate.docx');
        $move = Tb_sinhvien::join('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
                ->join('tb_giay_dc_diaphuong', 'tb_giay_dc_diaphuong.MaGiayDK', '=', 'tb_giay_cn_dangky.MaGiayDK')
                ->join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
                ->join('tb_khoa','tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
                ->select('tb_sinhvien.HoTen', 'tb_sinhvien.MaSinhVien', 'tb_sinhvien.NgaySinh', 'tb_sinhvien.TinhTrangSinhVien', 
                'tb_giay_cn_dangky.MaGiayDK', 'tb_giay_cn_dangky.SoDangKy', 'tb_giay_cn_dangky.NgayDangKy', 'tb_giay_cn_dangky.NoiDangKy',
                'tb_giay_dc_diaphuong.NoiOHienTai', 'tb_giay_dc_diaphuong.BanChiHuy'
                );

        if($request->MaSinhVien){
            $move = $move->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if($request->TenLop){
            $move = $move->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if($request->TenKhoa){
            $move = $move->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if($request->Khoas){
            $move = $move->where('tb_lop.Khoas', '=', $request->Khoas);
        }

        $move = $move->where(function ($query) {
            $query->where('tb_sinhvien.TinhTrangSinhVien', '=', 'Đã tốt nghiệp')
                ->orWhere('tb_sinhvien.TinhTrangSinhVien', '=', 'Đã thôi học');
        });

        $count = $move->count();
        $move = $move->get();

        // ///NgayCap
        $NgayCap = Carbon::now()->toDateString();
        $NgayCap =  explode("-", $NgayCap);

        //Ngay het han
        $Now = Carbon::now();
        $NgayHH = $Now->addDays(10)->toDateString();
        $NgayHH =  explode("-", $NgayHH);

        if($count!=0){
            for($i = 0; $i<$count; $i++){
                
                ///NgaySinh
                $NgaySinh = explode("-", $move[$i]["NgaySinh"]);
                
                //Ngay Dang ky
                $NgayDangKy = explode("-", $move[$i]["NgayDangKy"]);
                $NgayDangKy = $NgayDangKy[2][0].$NgayDangKy[2][1] . '/' . $NgayDangKy[1] . '/' . $NgayDangKy[0];
                
                Tb_giay_dc_truong::insert([
                    'SoGioiThieuDC' => $request->SoGioiThieuDC,
                    'NgayCap'       => Carbon::now()->toDateString(), 
                    'NgayHH'        => Carbon::now()->addDays(10)->toDateString(),
                    'NoiChuyenVe'   => $move[$i]["BanChiHuy"],
                    'NoiOHienTai'   => $move[$i]["NoiOHienTai"],
                    'LyDo'          => $move[$i]["TinhTrangSinhVien"],
                    'MaGiayDK'      => $move[$i]["MaGiayDK"],
                ]);

                $array1 = array(
                    'HoTen'            => $move[$i]["HoTen"],
                    'SoGioiThieuDC'    => $request->SoGioiThieuDC,
                    'SoDangKy'         => $move[$i]["SoDangKy"],
                    'NoiDangKy'        => $move[$i]["NoiDangKy"],
                    'NgayDangKy'       => $NgayDangKy,
                    'Ngay'             => $NgayCap[2],
                    'Thang'            => $NgayCap[1],
                    'Nam'              => $NgayCap[0],
                    'NgaySinh'         => $NgaySinh[2][0].$NgaySinh[2][1],
                    'ThangSinh'        => $NgaySinh[1],
                    'NamSinh'          => $NgaySinh[0],
                    'NoiOHienTai'      => $move[$i]["NoiOHienTai"],
                    'NoiChuyenVe'      => $move[$i]["BanChiHuy"],
                    'LyDo'             => $move[$i]["TinhTrangSinhVien"],
                    'NgayHH'           => $NgayHH[2],
                    'ThangHH'          => $NgayHH[1],
                    'NamHH'            => $NgayHH[0],
                    'i'                => $i + 1,
                    );

                $array[] = $array1;
            }
            $templateProcessor->cloneBlock('block_name', 0, true, false, $array);   
            $filename = "DanhSachGiayDiChuyen";
            $templateProcessor->saveAs($filename . '.docx');
            return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        }
        else{
            return response()->json(['status'=>'Not Found!']);
        }
    }
}
