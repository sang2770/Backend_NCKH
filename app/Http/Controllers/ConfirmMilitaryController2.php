<?php

namespace App\Http\Controllers;

use App\Models\Tb_canbo;
use App\Models\Tb_giay_xn_truong;
use App\Models\Tb_sinhvien;
use App\Models\Tb_yeucau;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class ConfirmMilitaryController2 extends Controller
{
    public function Confirm(Request $request)
    {
        $NgayCap = Carbon::now()->toDateString();
        $NgayCap =  explode("-", $NgayCap);
        $Ngay = $NgayCap[2];
        $Thang = $NgayCap[1];
        $Nam = $NgayCap[0];

        $canbo = Tb_canbo::select('ChucVu')->where('HoVaTen', '=', $request->HoVaTen)->get();

        ///insert yeucau
        $req = Tb_sinhvien::join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
        ->join('tb_khoa', 'tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
        ->join('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
        ->select(
            'tb_sinhvien.MaSinhVien',
        );
        if ($request->MaSinhVien) {
            $req = $req->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $req = $req->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $req = $req->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $req = $req->where('tb_lop.Khoas', '=', $request->Khoas);
        }

        $countYC = $req->count();
        $req = $req->get();

        if ($countYC > 0) {
            for ($i = 0; $i < $countYC; $i++) {
                Tb_yeucau::insert([
                    'MaSinhVien' => $req[$i]["MaSinhVien"],
                    'NgayYeuCau' => Carbon::now()->toDateString(), 
                    'NgayXuLy' => Carbon::now()->toDateString(), 
                    'TrangThaiXuLy' => "Đã xử lý",
                    'LanXinCap' => "1",
                ]);
                
            }
        }

        ///
        $array = array();
        $templateProcessor = new TemplateProcessor('TemplateMilitary/ConfirmMilitaryTemplate.docx');
        $confirm = Tb_sinhvien::join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
            ->join('tb_khoa', 'tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
            ->join('tb_yeucau', 'tb_yeucau.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
            ->select(
                'tb_sinhvien.HoTen',
                'tb_sinhvien.MaSinhVien',
                'tb_sinhvien.NgaySinh',
                'tb_lop.TenLop',
                'tb_khoa.TenKhoa',
                'tb_lop.Khoas',
                'tb_sinhvien.HeDaoTao',
                'tb_yeucau.MaYeuCau'
            );
        if ($request->MaSinhVien) {
            $confirm = $confirm->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $confirm = $confirm->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $confirm = $confirm->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $confirm = $confirm->where('tb_lop.Khoas', '=', $request->Khoas);
        }

        $confirm = $confirm->where('tb_yeucau.TrangThaiXuLy', '=', "Đã xử lý");

        $count = $confirm->count();
        $confirm = $confirm->get();

        if ($count != 0) {
            for ($i = 0; $i < $count; $i++) {

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

            Tb_yeucau::where('MaYeuCau', '=', $confirm[$i]['MaYeuCau'])->update(['TrangThaiXuLy' => 'Đã cấp']);

            $NgaySinh = explode("-", $confirm[$i]["NgaySinh"]);
            $NgaySinh = $NgaySinh[2][0].$NgaySinh[2][1] . "/" . $NgaySinh[1] . "/" . $NgaySinh[0];
            $array1 = array(
                'HoTen'     => $confirm[$i]["HoTen"], 
                'NgaySinh'  => $NgaySinh,
                'MaSinhVien'=> $confirm[$i]["MaSinhVien"],
                'TenLop'    => $confirm[$i]["TenLop"],
                'TenKhoa'   => $confirm[$i]["TenKhoa"],
                'Khoas'     => $confirm[$i]["Khoas"],
                'NamHoc'    => $NamHoc,
                'HeDaoTao'  => $confirm[$i]["HeDaoTao"],
                'Ngay'      => $Ngay,
                'Thang'     => $Thang,
                'Nam'       => $Nam,
                'ChiHuyTruong' => $request->HoVaTen,
                'ChucVu'       => mb_strtoupper($canbo[0]["ChucVu"], 'UTF-8'),
                'i'         => $i + 1
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

    public function Show($id){
        $info = Tb_sinhvien::select('tb_sinhvien.HoTen')
                            ->where('tb_sinhvien.MaSinhVien', '=', $id)->get();

        return response()->json(['status' => "Success", 'data' => $info[0]["HoTen"]]);
    }
}
