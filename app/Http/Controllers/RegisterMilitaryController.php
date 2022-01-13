<?php

namespace App\Http\Controllers;
use App\Models\Tb_giay_cn_dangky;
use App\Imports\RegisterMilitaryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\RegisterMilitaryRequest;
use App\Models\Tb_sinhvien;

class RegisterMilitaryController extends Controller
{
    // Import file
    public function StoreFile(Request $request){
        try {
            Excel::import(new RegisterMilitaryImport, $request->file);
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    //thêm từng giấy cn dky
    public function Store(RegisterMilitaryRequest $request){
        $validated = $request->validated();
        try {
            $validated = $request->safe()->only('SoDangKy', 'NgayDangKy', 'NoiDangKy', 'DiaChiThuongTru', 'NgayNop', 'MaSinhVien');
            Tb_giay_cn_dangky::insert($validated);
            return response()->json(['status' => "Success", 'data' => ["ThongTinDangKy" => $validated]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    /// loc ra thong tin sinh vien kem thong tin giay chung nhan dky
    public function Filter(Request $request){
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                            ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                            ->join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=' , 'Tb_sinhvien.MaSinhVien')
                            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_giay_cn_dangky.SoDangKy', 'Tb_giay_cn_dangky.NoiDangKy', 'Tb_giay_cn_dangky.NgayDangKy', 'Tb_giay_cn_dangky.NgayNop');

        if($request->MaSinhVien){
            $info = $info->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if($request->TenLop){
            $info = $info->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if($request->TenKhoa){
            $info = $info->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if($request->Khoas){
            $info = $info->where('Tb_lop.Khoas', '=', $request->Khoas);
        }
        $info = $info->get();
        return $info;
    }
}
