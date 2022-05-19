<?php

namespace App\Http\Controllers;

use App\Models\Tb_giay_cn_dangky;
use App\Imports\RegisterMilitaryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\RegisterMilitaryRequest;
use App\Http\Requests\UpdateRegister;
use App\Http\Requests\UpdateRegisterRequest;
use App\Models\Tb_giay_dc_diaphuong;
use App\Models\Tb_giay_xn_truong;
use App\Models\Tb_sinhvien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterMilitaryController extends Controller
{
    // Import file
    public function StoreFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        $import = new RegisterMilitaryImport();
        $import->import($request->file);
        if (!$import->failures()->isNotEmpty() && count($import->Err) == 0) {
            return response()->json(['status' => "Success"]);
        } else {
            $errors = [];
            $errorsMSV = [];
            foreach ($import->failures() as $value) {
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
            }
            foreach ($import->Err as $value) {
                $errorsMSV[] = ['row' => $value['row'], 'err' => $value['err']];
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'err' => $errors, 'errMSV' => $errorsMSV]);
        }
    }

    public function createRegister($Input){
        try {
            $date = date_create($Input['NgayDangKy']);
            $NgayDK = date_format($date, 'Y-m-d H:i:s');

            $date2 = date_create($Input['NgayNop']);
            $NgayNop = date_format($date2, 'Y-m-d H:i:s');
            
            return [
                'SoDangKy'          => $Input['SoDangKy'],
                'NgayDangKy'        => $NgayDK,
                'NoiDangKy'         => $Input['NoiDangKy'],
                'DiaChiThuongTru'   => $Input['DiaChiThuongTru'],
                'NgayNop'           => $NgayNop,
                'MaSinhVien'        => $Input['MaSinhVien'],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createMoveLocal($Input){
        try {
            $LyDo = "Trúng tuyển đại học, cao đẳng";

            $date = date_create($Input['NgayCap']);
            $NgayCap = date_format($date, 'Y-m-d H:i:s');

            return [
                'SoGioiThieu'          => $Input['SoGioiThieu'],
                'NgayCap'              => $NgayCap,
                'NoiOHienTai'          => $Input['NoiOHienTai'],
                'NoiChuyenDen'         => $Input['NoiChuyenDen'],
                'LyDo'                 => $LyDo,
                'BanChiHuy'            => $Input['BanChiHuy'],
                'MaSinhVien'           => $Input['MaSinhVien'],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //thêm từng giấy cn dky
    public function Store(RegisterMilitaryRequest $request)
    {
        $Register = $request->validated();

        try {
            $Register = $this->createRegister($request->all());
            Tb_giay_cn_dangky::insert($Register);

            return response()->json(['status' => "Success", 'data' => ["ThongTinDangKy" => $Register]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    //update
    public function edit($id)
    {
        $edit = Tb_giay_cn_dangky::where('MaSinhVien', $id)->first();
        return $edit;
    }

    public function Update(UpdateRegisterRequest $request, $id)
    {
        $request->validated();
        if (Tb_giay_cn_dangky::where('MaSinhVien', $id)->exists()) {
            $task = $this->edit($id);
            $input = $request->only('SoDangKy', 'NgayDangKy', 'NoiDangKy', 'DiaChiThuongTru', 'NgayNop');
            $task->fill($input)->save();
            return response()->json(['status' => "Success updated"]);
        } else {
            return response()->json(['status' => "Not Found!"]);
        }
    }
   /// loc ra thong tin sinh vien kem thong tin giay chung nhan dky
    public function FilterRegister(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
            ->join('tb_khoa', 'tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
            ->join('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
            ->join('tb_giay_dc_diaphuong', 'tb_sinhvien.MaSinhVien', '=' , 'tb_giay_dc_diaphuong.MaSinhVien')
            ->select(
                'tb_sinhvien.HoTen',
                'tb_sinhvien.MaSinhVien',
                'tb_sinhvien.NgaySinh',
                'tb_lop.TenLop',
                'tb_khoa.TenKhoa',
                'tb_lop.Khoas',
                'tb_giay_cn_dangky.SoDangKy',
                'tb_giay_cn_dangky.NoiDangKy',
                'tb_giay_cn_dangky.DiaChiThuongTru',
                'tb_giay_cn_dangky.NgayDangKy',
                'tb_giay_cn_dangky.NgayNop',
                'tb_giay_dc_diaphuong.SoGioiThieu',
                'tb_giay_dc_diaphuong.BanChiHuy',
                'tb_giay_dc_diaphuong.NgayCap',
                'tb_giay_dc_diaphuong.NoiOHienTai',
                'tb_giay_dc_diaphuong.NoiChuyenDen'
            );

        if ($request->MaSinhVien) {
            $info = $info->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('tb_lop.Khoas', '=', $request->Khoas);
        }
        if($request->NgayNop){
            $info = $info->whereYear('tb_giay_cn_dangky.NgayNop', '=', $request->NgayNop);
        }

        $info = $info->orderBy('NgayNop', 'DESC')->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }
    /// loc ra thong tin sinh vien kem thong tin giay di chuyen nvqs tu địa phương
    public function FilterMoveLocal(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
                            ->join('tb_khoa','tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
                            ->join('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=' , 'tb_sinhvien.MaSinhVien')
                            ->join('tb_giay_dc_diaphuong', 'tb_sinhvien.MaSinhVien', '=' , 'tb_giay_dc_diaphuong.MaSinhVien')
                            ->select('tb_sinhvien.HoTen', 'tb_sinhvien.MaSinhVien', 'tb_sinhvien.NgaySinh', 'tb_lop.TenLop', 
                            'tb_khoa.TenKhoa', 'tb_lop.Khoas', 'tb_giay_dc_diaphuong.MaGiayDC_DP', 'tb_giay_dc_diaphuong.SoGioiThieu', 'tb_giay_dc_diaphuong.BanChiHuy',
                            'tb_giay_dc_diaphuong.NgayCap', 'tb_giay_dc_diaphuong.NoiOHienTai', 'tb_giay_dc_diaphuong.NoiChuyenDen');

        if ($request->MaSinhVien) {
            $info = $info->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('tb_lop.Khoas', '=', $request->Khoas);
        }

        $info = $info->orderBy('MaGiayDC_DP', 'DESC')->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }
    /// loc ra thong tin sinh vien kem thong tin giay xac nhan tu truong
    public function FilterConfirm(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
            ->join('tb_khoa', 'tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
            ->join('tb_yeucau', 'tb_yeucau.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
            ->select('tb_sinhvien.HoTen', 'tb_sinhvien.MaSinhVien', 'tb_sinhvien.NgaySinh', 
            'tb_lop.TenLop', 'tb_khoa.TenKhoa', 'tb_lop.Khoas', 'tb_yeucau.TrangThaiXuLy', 'tb_yeucau.MaYeuCau');

        if ($request->MaSinhVien) {
            $info = $info->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('tb_lop.Khoas', '=', $request->Khoas);
        }
        if ($request->TrangThaiXuLy) {
            $info = $info->where('tb_yeucau.TrangThaiXuLy', '=', $request->TrangThaiXuLy);
        }

        $info = $info->where(function ($query) {
            $query->where('tb_yeucau.TrangThaiXuLy', '=', 'Đã xử lý')
                ->orWhere('tb_yeucau.TrangThaiXuLy', '=', 'Đã cấp');
        });

        $info = $info->orderBy('MaYeuCau', 'DESC')->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }

    /// loc ra thong tin sinh vien kem thong tin giay di chuyen nvqs tu truong
    public function FilterMove(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
            ->join('tb_khoa', 'tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
            ->join('tb_trangthai', 'tb_trangthai.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
            ->leftJoin('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
            ->leftJoin('tb_giay_dc_truong', 'tb_giay_dc_truong.MaGiayDK', '=', 'tb_giay_cn_dangky.MaGiayDK')
            ->select('tb_sinhvien.HoTen', 'tb_sinhvien.MaSinhVien', 'tb_sinhvien.NgaySinh', 
            'tb_lop.TenLop', 'tb_khoa.TenKhoa', 'tb_lop.Khoas', 'tb_sinhvien.TinhTrangSinhVien',
            'tb_trangthai.SoQuyetDinh', 'tb_trangthai.NgayQuyetDinh'
             , DB::raw('count(tb_giay_dc_truong.MaGiayDC_Truong) as total'))
             ->groupBy('tb_sinhvien.MaSinhVien');

        if ($request->MaSinhVien) {
            $info = $info->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('tb_lop.Khoas', '=', $request->Khoas);
        }
        if ($request->TinhTrangSinhVien) {
            $info = $info->where('tb_sinhvien.TinhTrangSinhVien', '=', $request->TinhTrangSinhVien);
        }
        if ($request->NgayQuyetDinh) {
            $info = $info->whereYear('tb_trangthai.NgayQuyetDinh', '=', $request->NgayQuyetDinh);
        }
        if ($request->SoQuyetDinh) {
            $info = $info->where('tb_trangthai.SoQuyetDinh', '=', $request->SoQuyetDinh);
        }

        // $info = $info->where(function ($query) {
        //     $query->where('tb_sinhvien.TinhTrangSinhVien', '=', 'Đã tốt nghiệp')
        //         ->orWhere('tb_sinhvien.TinhTrangSinhVien', '=', 'Thôi học');
        // });

        $info = $info->orderBy('NgayQuyetDinh', 'DESC')->distinct()->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info['data'], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }

    /// loc ra thong tin sinh vien da co giay cn dky nvqs kèm số lần đã cấp giấy xác nhận
    public function FilterStudentRegister(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info =Tb_sinhvien::join('tb_lop', 'tb_lop.MaLop', '=', 'tb_sinhvien.MaLop')
            ->join('tb_khoa', 'tb_khoa.MaKhoa', '=', 'tb_lop.MaKhoa')
            ->join('tb_giay_cn_dangky', 'tb_giay_cn_dangky.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
            ->leftJoin('tb_yeucau', 'tb_yeucau.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
            ->leftJoin('tb_giay_xn_truong', 'tb_giay_xn_truong.MaYeuCau', '=', 'tb_yeucau.MaYeuCau')
            ->select('tb_sinhvien.HoTen', 'tb_sinhvien.MaSinhVien', 'tb_sinhvien.NgaySinh', 
            'tb_lop.TenLop', 'tb_khoa.TenKhoa', 'tb_lop.Khoas', 'tb_giay_cn_dangky.NgayNop', 
            'tb_giay_xn_truong.NgayCap', "tb_giay_xn_truong.MaGiayXN_Truong"
             , DB::raw('count(tb_giay_xn_truong.MaGiayXN_Truong) as total'))
             ->groupBy('tb_sinhvien.MaSinhVien');
        if ($request->MaSinhVien) {
            $info = $info->where('tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('tb_lop.Khoas', '=', $request->Khoas);
        }
        if ($request->NgayCap) {
            $info = $info->whereYear('tb_giay_xn_truong.NgayCap', '=', $request->NgayCap);
        }
        $info = $info->orderBy('MaGiayDK', 'DESC')->distinct()->paginate($limit, ["*"], 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info['data'], 'pagination' => [
            "page" => $info['current_page'],
            "limit" => $limit,
            "TotalPage" => $info['last_page']
        ]]);
    }
}
