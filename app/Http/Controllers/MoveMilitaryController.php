<?php

namespace App\Http\Controllers;

use App\Models\Tb_canbo;
use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_sinhvien;
use App\Models\Tb_giay_dc_truong;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Nette\Utils\ArrayList;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;
use PhpOffice\PhpWord\TemplateProcessor;

class MoveMilitaryController extends Controller
{
    public function Move(Request $request){
            $array = array();
            $templateProcessor = new TemplateProcessor('TemplateMilitary/MoveMilitaryTemplate.docx');
            $move = Tb_sinhvien::join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
                    ->join('Tb_giay_dc_diaphuong', 'Tb_giay_dc_diaphuong.MaGiayDK', '=', 'Tb_giay_cn_dangky.MaGiayDK')
                    ->join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                    ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                    ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_sinhvien.TinhTrangSinhVien', 
                    'Tb_giay_cn_dangky.MaGiayDK', 'Tb_giay_cn_dangky.SoDangKy', 'Tb_giay_cn_dangky.NgayDangKy', 'Tb_giay_cn_dangky.NoiDangKy',
                    'Tb_giay_dc_diaphuong.NoiOHienTai', 'Tb_giay_dc_diaphuong.BanChiHuy'
                    );

            if($request->MaSinhVien){
                $move = $move->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
            }
            if($request->TenLop){
                $move = $move->where('Tb_lop.TenLop', '=', $request->TenLop);
            }
            if($request->TenKhoa){
                $move = $move->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
            }
            if($request->Khoas){
                $move = $move->where('Tb_lop.Khoas', '=', $request->Khoas);
            }
            if($request->TinhTrangSinhVien){
                $move = $move->where('Tb_sinhvien.TinhTrangSinhVien', '=', $request->TinhTrangSinhVien);
            }

            $move = $move->where(function ($query) {
                $query->where('Tb_sinhvien.TinhTrangSinhVien', '=', 'Đã tốt nghiệp')
                    ->orWhere('Tb_sinhvien.TinhTrangSinhVien', '=', 'Đã thôi học');
            });

            $count = $move->count();
            $move = $move->get();

            // ///NgayCap
            $NgayCap = Carbon::now()->toDateString();
            $NgayCap =  explode("-", $NgayCap);

            //Ngay het han
            // $Now = Carbon::now();
            // $NgayHH = $Now->addDays(10)->toDateString();
            // $NgayHH =  explode("-", $NgayHH);

            $countMove = Tb_giay_dc_truong::WhereYear('NgayCap', '=', $NgayCap[0])->count();
            
            if($countMove == 0){
                $countMove = 1;
            }elseif($countMove > 0){
                $countMove += 1;
            }
            $canbo = Tb_canbo::select('HoVaTen')->where('ThoiGianKetThuc', '>=', $NgayCap)->where('TrangThai', '=', 'Đang hoạt động')->get();

            if($count!=0){
                for($i = 0; $i<$count; $i++){
                    
                    ///NgaySinh
                    $NgaySinh = explode("-", $move[$i]["NgaySinh"]);
                    
                    //Ngay Dang ky
                    $NgayDangKy = explode("-", $move[$i]["NgayDangKy"]);
                    $namDK = $NgayDangKy[0];
                    $NgayDangKy = $NgayDangKy[2][0].$NgayDangKy[2][1] . '/' . $NgayDangKy[1] . '/' . $NgayDangKy[0];
                    
                    Tb_giay_dc_truong::insert([
                        'SoGioiThieuDC' => $countMove,
                        'NgayCap'       => Carbon::now()->toDateString(), 
                        'NgayHH'        => $request->NamHH . '-' . $request->ThangHH . '-' . $request->NgayHH,
                        'NoiChuyenVe'   => $move[$i]["BanChiHuy"],
                        'NoiOHienTai'   => $move[$i]["NoiOHienTai"],
                        'LyDo'          => $move[$i]["TinhTrangSinhVien"],
                        'MaGiayDK'      => $move[$i]["MaGiayDK"],
                    ]);

                    $array1 = array(
                        'HoTen'            => $move[$i]["HoTen"],
                        'SoGioiThieuDC'    => $countMove,
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
                        'NgayHH'           => $request->NgayHH,
                        'ThangHH'          => $request->ThangHH,
                        'NamHH'            => $request->NamHH,
                        'namDK'            => $namDK,
                        'ChiHuyTruong'     => $canbo[0]["HoVaTen"],
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

    //
    //show lần cấp của từng sinh viên
    public function show(Request $request, $id){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
                            ->join('Tb_giay_dc_truong','Tb_giay_dc_truong.MaGiayDK', '=', 'Tb_giay_cn_dangky.MaGiayDK')
                            ->select('Tb_giay_dc_truong.NgayCap', 'Tb_giay_dc_truong.NgayHH', 'Tb_giay_dc_truong.LyDo', 'Tb_giay_dc_truong.NoiChuyenVe')
                            ->where('Tb_sinhvien.MaSinhVien', '=', $id);

        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }
}
