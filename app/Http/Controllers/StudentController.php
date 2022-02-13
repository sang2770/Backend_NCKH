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
            $LanCap = Tb_yeucau::where('MaSinhVien', $Input['MaSinhVien'])->count();
            return [
                'MaSinhVien'        => $Input['MaSinhVien'],
                'NgayYeuCau'        => $NgayYeuCau,
                'NgayXuLy'          => $NgayYeuCau,
                'TrangThaiXuLy'     => 'Chờ xử lý',
                'LanXinCap'         => $LanCap + 1
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
    public function register(Request $request, $id)
    {
        $info = Tb_sinhvien::join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->where('Tb_sinhvien.MaSinhVien', '=', $id)
            ->select(
                'Tb_sinhvien.HoTen',
                'Tb_sinhvien.NgaySinh',
                'Tb_giay_cn_dangky.SoDangKy',
                'Tb_giay_cn_dangky.NgayDangKy',
                'Tb_giay_cn_dangky.NoiDangKy',
                'Tb_giay_cn_dangky.DiaChiThuongTru'
            );

        if ($info->exists()) {
            $info = $info->first();
            return response()->json(['status' => "Success", 'data' => $info]);
        } else {
            return response()->json(['status' => "Not Found!"]);
        }
    }

    //danh sach tieu de thong bao
    public function notification(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $noti = Tb_sinhvien::join('Tb_tk_sinhvien', 'Tb_tk_sinhvien.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->join('Tb_thongbaosv', 'Tb_thongbaosv.MaTKSV', '=', 'Tb_tk_sinhvien.MaTKSV')
            ->join('Tb_thongbaochinh', 'Tb_thongbaochinh.MaThongBaoChinh', '=', 'Tb_thongbaosv.MaThongBaoChinh')
            ->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien)
            ->select('Tb_thongbaochinh.TieuDeTB', 'Tb_thongbaochinh.MaThongBaoChinh', 'Tb_sinhvien.MaSinhVien');

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

    //sv xem chi tiet tung thong bao
    public function notificationID(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $noti = Tb_sinhvien::join('Tb_tk_sinhvien', 'Tb_tk_sinhvien.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->join('Tb_thongbaosv', 'Tb_thongbaosv.MaTKSV', '=', 'Tb_tk_sinhvien.MaTKSV')
            ->join('Tb_thongbaochinh', 'Tb_thongbaochinh.MaThongBaoChinh', '=', 'Tb_thongbaosv.MaThongBaoChinh')
            ->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien)
            ->where('Tb_thongbaosv.MaThongBaoChinh', '=', $request->MaThongBaoChinh)
            ->select('Tb_thongbaochinh.TieuDeTB', 'Tb_thongbaochinh.NoiDungTB');

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

    //sv xem danh sach các giay xac nhan đã xin cấp
    public function showRequest(Request $request, $id)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_yeucau::where('Tb_yeucau.MaSinhVien', '=', $id)
            ->select('MaYeuCau', 'MaSinhVien', 'NgayYeuCau', 'NgayXuLy', 'TrangThaiXuLy');

        if ($info->exists()) {
            $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
            return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
                "page" => $info['current_page'],
                "first_page_url"    => $info['first_page_url'],
                "next_page_url"     => $info['next_page_url'],
                "TotalPage"         => $info['last_page']
            ]]);
        } else {
            return response()->json(['status' => "Not Found!"]);
        }
    }

    //xoa yeu cau xac nhan (chi xoa những yêu cầu chưa cấp (đã cấp thì k thể xóa yêu cầu))
    public function DestroyRequest($id, $msv)
    {
        if (Tb_yeucau::where('MaYeuCau', $id)
            ->where('MaSinhVien', $msv)
            ->where(function ($query) {
                $query->where('Tb_yeucau.TrangThaiXuLy', '=', 'Đã xử lý')
                    ->orWhere('Tb_yeucau.TrangThaiXuLy', '=', 'Chờ xử lý');
            })->exists()
        ) {
            Tb_yeucau::where('MaYeuCau', $id)->delete();
            return response()->json(['status' => "Success deleted"]);
        } else {
            return response()->json(['status' => "Not Found!"]);
        }
    }
}
