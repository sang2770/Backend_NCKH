<?php

namespace App\Http\Controllers;

use App\Models\Tb_giay_xn_truong;
use App\Models\Tb_sinhvien;
use App\Models\Tb_yeucau;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ConfirmMilitaryController extends Controller
{
    public function Confirm(Request $request)
    { //MaYeuCau, NamHoc
        $array = array();
        $templateProcessor = new TemplateProcessor('TemplateMilitary/ConfirmMilitaryTemplate.docx');
        $confirm = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
            ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
            ->join('Tb_yeucau', 'Tb_yeucau.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->select(
                'Tb_sinhvien.HoTen',
                'Tb_sinhvien.MaSinhVien',
                'Tb_sinhvien.NgaySinh',
                'Tb_lop.TenLop',
                'Tb_khoa.TenKhoa',
                'Tb_lop.Khoas',
                'Tb_sinhvien.HeDaoTao',
                'Tb_yeucau.MaYeuCau'
            );
        if ($request->HoTen) {
            $confirm = $confirm->where('HoTen', 'LIKE', '%' . $request->HoTen . '%');
        }
        if ($request->MaSinhVien) {
            $confirm = $confirm->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $confirm = $confirm->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $confirm = $confirm->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $confirm = $confirm->where('Tb_lop.Khoas', '=', $request->Khoas);
        }
        if($request->TrangThaiXuLy){
            $confirm = $confirm->where('Tb_yeucau.TrangThaiXuLy', '=', $request->TrangThaiXuLy);
        }

        $confirm = $confirm->where(function ($query) {
            $query->where('Tb_yeucau.TrangThaiXuLy', '=', 'Đã xử lý')
                ->orWhere('Tb_yeucau.TrangThaiXuLy', '=', 'Đã cấp');
        });

        if ($request->MaYeuCau) {
            $confirm = $confirm->where("tb_yeucau.MaYeuCau", "=", $request->MaYeuCau);
        }
        $count = $confirm->count();
        $confirm = $confirm->get();
        if ($count != 0) {
            for ($i = 0; $i < $count; $i++) {

                Tb_giay_xn_truong::insert([
                    'NgayCap' => Carbon::now()->toDateString(),
                    'NamHoc'  => $request->NamHoc,
                    'MaYeuCau' => $confirm[$i]['MaYeuCau']
                ]);

<<<<<<< HEAD
            $NgayCap = Carbon::now()->toDateString();
            $NgayCap =  explode("-", $NgayCap);
            $Ngay = $NgayCap[2];
            $Thang = $NgayCap[1];
            $Nam = $NgayCap[0];

            if($Thang < 8){
                $NamHoc = ($Nam -1 ) . " - " . $Nam ;
            }
            if($Thang >= 8){
                $NamHoc = ($Nam) . " - " . ($Nam+1) ;
            }

            Tb_giay_xn_truong::insert([
                'NgayCap' => Carbon::now()->toDateString(), 
                'NamHoc'  => $NamHoc,
                'MaYeuCau'=> $confirm[$i]['MaYeuCau']
            ]);

            $NgaySinh = explode("-", $confirm[$i]["NgaySinh"]);
            $NgaySinh = $NgaySinh[2][0].$NgaySinh[2][1] . "/" . $NgaySinh[1] . "/" . $NgaySinh[0];

            $array1 = array(
                'HoTen'     => $confirm[$i]["HoTen"], 
                'NgaySinh'  => $confirm[$i]["NgaySinh"],
                'MaSinhVien'=> $confirm[$i]["MaSinhVien"],
                'TenLop'    => $confirm[$i]["TenLop"],
                'TenKhoa'   => $confirm[$i]["TenKhoa"],
                'Khoas'     => $confirm[$i]["Khoas"],
                'NamHoc'    => $NamHoc,
                'HeDaoTao'  => $confirm[$i]["HeDaoTao"],
                'Ngay'      => $Ngay,
                'Thang'     => $Thang,
                'Nam'       => $Nam,
                'i'         => $i + 1
=======
                $NgayCap = Carbon::now()->toDateString();
                $NgayCap =  explode("-", $NgayCap);
                $Ngay = $NgayCap[2];
                $Thang = $NgayCap[1];
                $Nam = $NgayCap[0];

                $NgaySinh = explode("-", $confirm[$i]["NgaySinh"]);
                $NgaySinh = $NgaySinh[2][0] . $NgaySinh[2][1] . "/" . $NgaySinh[1] . "/" . $NgaySinh[0];

                $array1 = array(
                    'HoTen'     => $confirm[$i]["HoTen"],
                    'NgaySinh'  => $confirm[$i]["NgaySinh"],
                    'MaSinhVien' => $confirm[$i]["MaSinhVien"],
                    'TenLop'    => $confirm[$i]["TenLop"],
                    'TenKhoa'   => $confirm[$i]["TenKhoa"],
                    'Khoas'     => $confirm[$i]["Khoas"],
                    'NamHoc'    => $request->NamHoc,
                    'HeDaoTao'  => $confirm[$i]["HeDaoTao"],
                    'Ngay'      => $Ngay,
                    'Thang'     => $Thang,
                    'Nam'       => $Nam,
                    'i'         => $i + 1
>>>>>>> 660f79a9d0655ca749fd1046d5072bec7b30d65b
                );

                $array[] = $array1;
            }
            $templateProcessor->cloneBlock('block_name', 0, true, false, $array);
            $filename = "DanhSachGiayXN";
            $templateProcessor->saveAs($filename . '.docx');
            return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        } else {
            return response()->json(['status' => 'Not Found!']);
        }
    }
}
