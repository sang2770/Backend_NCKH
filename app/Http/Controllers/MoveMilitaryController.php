<?php

namespace App\Http\Controllers;

use App\Models\Tb_canbo;
use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_sinhvien;
use App\Models\Tb_giay_dc_truong;
use App\Models\Tb_trangthai;
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
        $move = Tb_sinhvien::join('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
                ->join('tb_giay_dc_diaphuong', 'tb_giay_dc_diaphuong.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
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
        if($request->TinhTrangSinhVien){
            $move = $move->where('tb_sinhvien.TinhTrangSinhVien', '=', $request->TinhTrangSinhVien);
        }
        if ($request->NgayQuyetDinh) {
            $move = $move->whereYear('tb_trangthai.NgayQuyetDinh', '=', $request->NgayQuyetDinh);
        }
        if ($request->SoQuyetDinh) {
            $move = $move->where('tb_trangthai.SoQuyetDinh', '=', $request->SoQuyetDinh);
        }

        // $move = $move->where(function ($query) {
        //     $query->where('tb_sinhvien.TinhTrangSinhVien', '=', '???? t???t nghi???p')
        //         ->orWhere('tb_sinhvien.TinhTrangSinhVien', '=', 'Th??i h???c');
        // });

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
        // $canbo = Tb_canbo::select('HoVaTen')->where('ThoiGianKetThuc', '>=', $NgayCap)
        // ->where('TrangThai', '=', '??ang ho???t ?????ng')
        // ->where('ChucVu', '=', 'Ch??? huy tr?????ng')
        // ->get();

        $canbo = Tb_canbo::select('ChucVu')->where('HoVaTen', '=', $request->HoVaTen)->get();

        if($count!=0){
            for($i = 0; $i<$count; $i++){
                $bch = ucwords(mb_strtolower(substr($move[$i]["BanChiHuy"], 0, 3), 'UTF-8')); // Ban
                $chqs = mb_strtoupper(substr($move[$i]["BanChiHuy"], 3, 6), 'UTF-8'); // CHQS
                
                $v = mb_strtoupper($move[$i]["BanChiHuy"], 'UTF-8');
                $xaV = explode("X??", $v);
                $countV = count($xaV);
                $xa = "x??";
                if($countV == 1){
                    $xaV = explode("PH?????NG", $v);
                    $countV = count($xaV);
                    $xa = "ph?????ng";
                    if($countV == 1){
                        $xaV = explode("TH??? TR???N", $v);
                        $countV = count($xaV);
                        $xa = "th??? tr???n";
                        if($countV == 1){
                            $xaV = explode("HUY???N", $v);
                            $countV = count($xaV);
                            $xa = "huy???n";
                            if($countV == 1){
                                $xaV = explode("CHQS", $v);
                                $countV = count($xaV);
                                $xa = "";
                            }
                        }
                    }
                }
                $kq = ucwords(mb_strtolower($xaV[1], 'UTF-8')); // t??n x?? ph?????ng

                // var_dump($countV);
                // var_dump($xaV);
                // var_dump($xa);
                // var_dump($kq);

                $bchqs = $bch . $chqs . $xa . $kq;
                // var_dump($bchqs);
                ///NgaySinh
                $NgaySinh = explode("-", $move[$i]["NgaySinh"]);
                
                //Ngay Dang ky
                $NgayDangKy = explode("-", $move[$i]["NgayDangKy"]);
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
                    'HoTen'            => mb_strtoupper($move[$i]["HoTen"], 'UTF-8'),
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
                    'NoiChuyenVe'      => $bchqs,
                    'LyDo'             => $move[$i]["TinhTrangSinhVien"],
                    'NgayHH'           => $request->NgayHH,
                    'ThangHH'          => $request->ThangHH,
                    'NamHH'            => $request->NamHH,
                    'ChiHuyTruong'     => $request->HoVaTen,
                    'ChucVu'           => mb_strtoupper($canbo[0]["ChucVu"], 'UTF-8'),
                    'i'                => $i + 1,
                    );

                $array[] = $array1;
            }
            $templateProcessor->cloneBlock('block_name', 0, true, false, $array);   
            $filename = "DanhSachGiayDiChuyen";
            $templateProcessor->saveAs($filename . '.docx');
            // return response()->json(['status'=>'Success']);
            return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        }
        else{
            return response()->json(['status'=>'Not Found!']);
        }
    }

    //
    //show l???n c???p c???a t???ng sinh vi??n
    public function show(Request $request, $id){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
                            ->join('tb_giay_dc_truong','tb_giay_dc_truong.MaGiayDK', '=', 'tb_giay_cn_dangky.MaGiayDK')
                            ->select('tb_giay_dc_truong.NgayCap', 'tb_giay_dc_truong.NgayHH', 'tb_giay_dc_truong.LyDo', 'tb_giay_dc_truong.NoiChuyenVe')
                            ->where('tb_sinhvien.MaSinhVien', '=', $id);

        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }

    public function list(){
        try {
            $lst = Tb_trangthai::distinct()->pluck("SoQuyetDinh");
            return response()->json(['status' => "Success", 'data' => $lst]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
}
