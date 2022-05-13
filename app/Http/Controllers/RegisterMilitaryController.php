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
        $MagiayDK = Tb_giay_cn_dangky::select('MaGiayDK')->where('Tb_giay_cn_dangky.MaSinhVien', '=', $Input['MaSinhVien'])->get();
        $count = Tb_giay_dc_diaphuong::select('MaGiayDK')->where('Tb_giay_dc_diaphuong.MaGiayDK', '=', $MagiayDK[0]['MaGiayDK'])->count();

        if($count>0){
            return null;
        }
        else{
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
                    'MaGiayDK'             => $MagiayDK[0]['MaGiayDK'],
                ];
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    //thêm từng giấy cn dky
    public function Store(RegisterMilitaryRequest $request)
    {
        $Register = $request->validated();
        $MoveLocal = $request->validated();

        try {
            $Register = $this->createRegister($request->all());
            Tb_giay_cn_dangky::insert($Register);

            $MoveLocal = $this->createMoveLocal($request->all());

            Tb_giay_dc_diaphuong::insert($MoveLocal);
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
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
            ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
            ->join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->join('Tb_giay_dc_diaphuong', 'Tb_giay_cn_dangky.MaGiayDK', '=' , 'Tb_giay_dc_diaphuong.MaGiayDK')
            ->select(
                'Tb_sinhvien.HoTen',
                'Tb_sinhvien.MaSinhVien',
                'Tb_sinhvien.NgaySinh',
                'Tb_lop.TenLop',
                'Tb_khoa.TenKhoa',
                'Tb_lop.Khoas',
                'Tb_giay_cn_dangky.SoDangKy',
                'Tb_giay_cn_dangky.NoiDangKy',
                'Tb_giay_cn_dangky.DiaChiThuongTru',
                'Tb_giay_cn_dangky.NgayDangKy',
                'Tb_giay_cn_dangky.NgayNop',
                'Tb_giay_dc_diaphuong.SoGioiThieu',
                'Tb_giay_dc_diaphuong.BanChiHuy',
                'Tb_giay_dc_diaphuong.NgayCap',
                'Tb_giay_dc_diaphuong.NoiOHienTai',
                'Tb_giay_dc_diaphuong.NoiChuyenDen'
            );

        if ($request->MaSinhVien) {
            $info = $info->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('Tb_lop.Khoas', '=', $request->Khoas);
        }
        if($request->NgayNop){
            $info = $info->whereYear('Tb_giay_cn_dangky.NgayNop', '=', $request->NgayNop);
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
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                            ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                            ->join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=' , 'Tb_sinhvien.MaSinhVien')
                            ->join('Tb_giay_dc_diaphuong', 'Tb_giay_cn_dangky.MaGiayDK', '=' , 'Tb_giay_dc_diaphuong.MaGiayDK')
                            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_lop.TenLop', 
                            'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_giay_dc_diaphuong.MaGiayDC_DP', 'Tb_giay_dc_diaphuong.SoGioiThieu', 'Tb_giay_dc_diaphuong.BanChiHuy',
                            'Tb_giay_dc_diaphuong.NgayCap', 'Tb_giay_dc_diaphuong.NoiOHienTai', 'Tb_giay_dc_diaphuong.NoiChuyenDen');

        if ($request->MaSinhVien) {
            $info = $info->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('Tb_lop.Khoas', '=', $request->Khoas);
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
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
            ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
            ->join('Tb_yeucau', 'Tb_yeucau.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 
            'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_yeucau.TrangThaiXuLy', 'Tb_yeucau.MaYeuCau');

        if ($request->MaSinhVien) {
            $info = $info->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('Tb_lop.Khoas', '=', $request->Khoas);
        }
        if ($request->TrangThaiXuLy) {
            $info = $info->where('Tb_yeucau.TrangThaiXuLy', '=', $request->TrangThaiXuLy);
        }

        $info = $info->where(function ($query) {
            $query->where('Tb_yeucau.TrangThaiXuLy', '=', 'Đã xử lý')
                ->orWhere('Tb_yeucau.TrangThaiXuLy', '=', 'Đã cấp');
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
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
            ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
            ->join('Tb_trangthai', 'Tb_trangthai.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 
            'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_sinhvien.TinhTrangSinhVien', 
            'Tb_trangthai.SoQuyetDinh', 'Tb_trangthai.NgayQuyetDinh');

        if ($request->MaSinhVien) {
            $info = $info->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('Tb_lop.Khoas', '=', $request->Khoas);
        }
        if ($request->TinhTrangSinhVien) {
            $info = $info->where('Tb_sinhvien.TinhTrangSinhVien', '=', $request->TinhTrangSinhVien);
        }
        if ($request->NgayQuyetDinh) {
            $info = $info->whereYear('Tb_trangthai.NgayQuyetDinh', '=', $request->NgayQuyetDinh);
        }
        if ($request->SoQuyetDinh) {
            $info = $info->where('Tb_trangthai.SoQuyetDinh', '=', $request->SoQuyetDinh);
        }

        $info = $info->where(function ($query) {
            $query->where('Tb_sinhvien.TinhTrangSinhVien', '=', 'Đã tốt nghiệp')
                ->orWhere('Tb_sinhvien.TinhTrangSinhVien', '=', 'Thôi học');
        });

        $info = $info->orderBy('NgayQuyetDinh', 'DESC')->distinct()->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
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
        $info =DB::table("Tb_sinhvien")->join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
            ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
            ->join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->leftJoin('Tb_yeucau', 'Tb_yeucau.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->leftJoin('Tb_giay_xn_truong', 'Tb_giay_xn_truong.MaYeuCau', '=', 'Tb_yeucau.MaYeuCau')
            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 
            'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_giay_cn_dangky.NgayNop', 
            'Tb_giay_xn_truong.NgayCap', "Tb_giay_xn_truong.MaGiayXN_Truong"
             , DB::raw('count(Tb_giay_xn_truong.MaGiayXN_Truong) as total'))
             ->groupBy('Tb_sinhvien.MaSinhVien');
        if ($request->MaSinhVien) {
            $info = $info->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if ($request->TenLop) {
            $info = $info->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if ($request->TenKhoa) {
            $info = $info->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if ($request->Khoas) {
            $info = $info->where('Tb_lop.Khoas', '=', $request->Khoas);
        }
        if ($request->NgayCap) {
            $info = $info->whereYear('Tb_giay_xn_truong.NgayCap', '=', $request->NgayCap);
        }
        $info = $info->orderBy('MaGiayDK', 'DESC')->distinct()->paginate($limit, ["*"], 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info['data'], 'pagination' => [
            "page" => $info['current_page'],
            "limit" => $limit,
            "TotalPage" => $info['last_page']
        ]]);
    }
}
