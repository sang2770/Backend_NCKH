<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStudentRequest;
use App\Models\Tb_sinhvien;
use App\Models\Tb_yeucau;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

class StudentController extends Controller
{
    //thong tin sinh vien
    public function show(Request $request, $id)
    {
        if (Tb_sinhvien::where('MaSinhVien', '=', $id)->exists()) {
            $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                ->where('MaSinhVien', '=', $id)
                ->select('Tb_sinhvien.*', 'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas')->get([
                    'MaSinhVien', 'HoTen', 'NgaySinh', 'NoiSinh', 'GioiTinh', 'DanToc',
                    'TonGiao', 'QuocTich', 'DiaChiBaoTin', 'SDT', 'Email', 'HoKhauTinh', 'HoKhauHuyen',
                    'HoKhauXaPhuong', 'TinhTrangSinhVien', 'HeDaoTao', 'TenKhoa', 'TenLop', 'SoCMTND', 'NgayCapCMTND', 'NoiCapCMTND'
                ])->first();

            return response()->json(["status" => "Success", 'data' => $info]);
        } else {
            return response()->json(['status' => "Not Found!!!"]);
        }
    }

    public function create($Input)
    {
        try {
            $NgayYeuCau = Carbon::now()->toDateString();
            return [
                'MaSinhVien'        => $Input['MaSinhVien'],
                'NgayYeuCau'        => $NgayYeuCau,
                'NgayXuLy'          => $NgayYeuCau,
                'TrangThaiXuLy'     => 'Chờ xử lý'
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //sinh vien gui yeu cau
    public function store(RequestStudentRequest $request)
    {
        $req = $request->validated();
        try {
            $req = $this->create($request->all());
            Tb_yeucau::insert($req);
            return response()->json(['status' => "Success", 'data' => $req]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    //sv xem thong tin giay chung nhan dky nvqs
    public function register(Request $request)
    {
        $info = Tb_sinhvien::join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien)
            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.NgaySinh', 'Tb_giay_cn_dangky.SoDangKy', 'Tb_giay_cn_dangky.NgayDangKy', 'Tb_giay_cn_dangky.NoiDangKy', 'Tb_giay_cn_dangky.DiaChiThuongTru');

        if ($info->exists()) {
            $info = $info->get();
            return response()->json(['status' => "Success", 'data' => $info]);
        } else {
            return response()->json(['status' => "Not Found!"]);
        }
    }

    //sv xem thong bao
    public function notification(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $noti = Tb_sinhvien::join('Tb_tk_sinhvien', 'Tb_tk_sinhvien.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->join('Tb_thongbaosv', 'Tb_thongbaosv.MaTKSV', '=', 'Tb_tk_sinhvien.MaTKSV')
            ->join('Tb_thongbaochinh', 'Tb_thongbaochinh.MaThongBaoChinh', '=', 'Tb_thongbaosv.MaThongBaoChinh')
            ->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien)
            ->select('Tb_thongbaochinh.TieuDeTB');

        if ($noti->exists()) {
            $noti = $noti->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
            return response()->json(['status' => "Success", 'data' => $noti["data"], 'pagination' => [
                "page" => $noti['current_page'],
                "first_page_url"    => $noti['first_page_url'],
                "next_page_url"     => $noti['next_page_url'],
                "TotalPage"         => $noti['last_page']
            ]]);
        } else {
            return response()->json(['status' => "Not Found!"]);
        }
    }
}
