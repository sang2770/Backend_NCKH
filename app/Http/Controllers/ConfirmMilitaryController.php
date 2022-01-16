<?php

namespace App\Http\Controllers;

use App\Models\Tb_giay_xn_truong;
use App\Models\Tb_sinhvien;
use App\Models\Tb_yeucau;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class ConfirmMilitaryController extends Controller
{
    public function Confirm(Request $request){ //MaYeuCau, NamHoc
        if(Tb_yeucau::where('MaYeuCau', '=', $request->MaYeuCau)
            ->where(function ($query) {
                $query->where('TrangThaiXuLy', '=', 'Đã xử lý')
                    ->orWhere('TrangThaiXuLy', '=', 'Đã cấp');
        })->exists()){
            $confirm = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                                ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                                ->join('Tb_yeucau', 'Tb_yeucau.MaSinhVien', '=' , 'Tb_sinhvien.MaSinhVien')
                                ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_sinhvien.HeDaoTao')
                                ->where('Tb_yeucau.MaYeuCau', '=', $request->MaYeuCau)
                                ->get();

            Tb_giay_xn_truong::insert([
                'NgayCap' => Carbon::now()->toDateString(), 
                'NamHoc'  => $request->NamHoc,
                'MaYeuCau'=> $request->MaYeuCau
            ]);

            $NgayCap = Carbon::now()->toDateString();
            $NgayCap =  explode("-", $NgayCap);
            $Ngay = $NgayCap[2];
            $Thang = $NgayCap[1];
            $Nam = $NgayCap[0];

            $NgaySinh = explode("-", $confirm[0]["NgaySinh"]);
            $NgaySinh = $NgaySinh[2][0].$NgaySinh[2][1] . "/" . $NgaySinh[1] . "/" . $NgaySinh[0];

            $templateProcessor = new TemplateProcessor('TemplateMilitary/ConfirmMilitaryTemplate.docx');
            $templateProcessor->setValue('HoTen', $confirm[0]["HoTen"]);
            $templateProcessor->setValue('NgaySinh', $NgaySinh);
            $templateProcessor->setValue('MaSinhVien', $confirm[0]["MaSinhVien"]);
            $templateProcessor->setValue('TenLop', $confirm[0]["TenLop"]);
            $templateProcessor->setValue('TenKhoa', $confirm[0]["TenKhoa"]);
            $templateProcessor->setValue('Khoas', $confirm[0]["Khoas"]);
            $templateProcessor->setValue('NamHoc', $request->NamHoc);
            $templateProcessor->setValue('HeDaoTao', $confirm[0]["HeDaoTao"]);
            $templateProcessor->setValue('Ngay', $Ngay);
            $templateProcessor->setValue('Thang', $Thang);
            $templateProcessor->setValue('Nam', $Nam);

            $filename = $confirm[0]["HoTen"] . '_' . $confirm[0]["MaSinhVien"] . '_GiayXacNhanNVQS';
            $templateProcessor->saveAs($filename . '.docx');
            return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        }
        else{
            return response()->json(['status' => "Failed", 'err_message' => "Yêu cầu này chưa được xử lý. Bạn không thể cấp giấy!!!"]);
        }
    }
}
