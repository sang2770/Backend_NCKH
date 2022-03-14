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
use App\Models\Tb_sinhvien;
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

    public function create($Input){
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

    //thêm từng giấy cn dky
    public function Store(RegisterMilitaryRequest $request)
    {
        $validated = $request->validated();
        try {
            $validated = $this->create($request->all());
            Tb_giay_cn_dangky::insert($validated);
            return response()->json(['status' => "Success", 'data' => ["ThongTinDangKy" => $validated]]);
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
                'Tb_giay_cn_dangky.NgayNop'
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
        $info = $info->orderBy('NgayNop', 'DESC')->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
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
            ->leftJoin('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
            ->leftJoin('Tb_giay_dc_truong', 'Tb_giay_dc_truong.MaGiayDK', '=', 'Tb_giay_cn_dangky.MaGiayDK')
            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 
            'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_sinhvien.TinhTrangSinhVien', 
            'Tb_trangthai.SoQuyetDinh', 'Tb_trangthai.NgayQuyetDinh', 'Tb_giay_dc_truong.NgayCap');

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
        if($request->NgayCap == 0){
            $info = $info->where('Tb_giay_dc_truong.NgayCap', '=', null);
        }
        if($request->NgayCap == 1){
            $info = $info->where('Tb_giay_dc_truong.NgayCap', '<>', null);
        }
        if($request->NgayCap == 2){
            $info = $info->where(function ($query) {
                $query->where('Tb_giay_dc_truong.NgayCap', '=', null)
                    ->orWhere('Tb_giay_dc_truong.NgayCap', '<>', null);
            });
        }
        $info = $info->where(function ($query) {
            $query->where('Tb_sinhvien.TinhTrangSinhVien', '=', 'Đã tốt nghiệp')
                ->orWhere('Tb_sinhvien.TinhTrangSinhVien', '=', 'Đã thôi học');
        });

        $info = $info->orderBy('NgayQuyetDinh', 'DESC')->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
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
                            'Tb_giay_dc_diaphuong.NgayCap', 'Tb_giay_dc_diaphuong.NgayHH', 'Tb_giay_dc_diaphuong.NoiOHienTai', 'Tb_giay_dc_diaphuong.NoiChuyenDen');

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
}
