<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Helper\Helper;
use App\Http\Requests\AddStudentRequest;
use App\Http\Requests\UpdateUser;
use App\Imports\ClassImport;
use App\Imports\MajorImport;
use App\Imports\UpdateUserImport;
use App\Imports\UsersImport;
use App\Models\Tb_khoa;
use App\Models\Tb_lichsu;
use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use App\Models\Tb_tk_sinhvien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StudentManagementController extends Controller
{
    /**
     * Lấy danh sách sinh viên
     */
    public function index(Request $request)
    {
        // :/api/student-management/ /users?limit=?&page =?{filter } =?
        $limit = $request->query('limit');
        $page = $request->query('page');
        // Loc
        try {
            $user = Tb_sinhvien::join('Tb_lop', 'Tb_sinhvien.MaLop', '=', 'Tb_lop.MaLop')
                ->join('Tb_khoa', 'Tb_lop.MaKhoa', '=', 'Tb_khoa.MaKhoa')->filter($request)->paginate($limit, [
                    'MaSinhVien', 'HoTen', 'NgaySinh', 'NoiSinh', 'GioiTinh', 'DanToc',
                    'TonGiao', 'QuocTich', 'DiaChiBaoTin', 'SDT', 'Email', 'HoKhauTinh', 'HoKhauHuyen',
                    'HoKhauXaPhuong', 'TinhTrangSinhVien', 'HeDaoTao', 'TenKhoa', 'TenLop', 'SoCMTND', 'NgayCapCMTND', 'NoiCapCMTND'
                ], 'page', $page)->toArray();
            return response()->json(['status' => "Success", 'data' => $user['data'], 'pagination' => [
                "page" => $user['current_page'],
                "limit" => $limit,
                "TotalPage" => $user['last_page']
            ]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    public function exportIndex(Request $request)
    {
        try {
            return Excel::download(new UsersExport($request), 'DSSV_Filter.xlsx');
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    // Thêm đơn
    public function store(AddStudentRequest $request)
    {
        $validated = $request->validated();
        try {
            // get MaLop
            $TenLop = $request->TenLop;
            $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
            if (!$MaLop) {
                return response()->json(['status' => "Failed", 'Err_Message' => "MaLop không tồn tại"]);
            }
            $request->MaLop = $MaLop;
            $TaiKhoan = Helper::CreateUsers($request->safe()->only(["MaSinhVien", "NgaySinh", "HoTen"]));
            $validated = $request->safe()->except(['TenLop']);
            $validated['MaLop'] = $MaLop;
            // Begin trans
            DB::transaction(function () use ($validated, $TaiKhoan) { // Start the transaction
                Tb_sinhvien::create($validated);
                Tb_tk_sinhvien::create($TaiKhoan);
            });
            return response()->json(['status' => "Success", 'data' => ["SinhVien" => $validated, "TaiKhoan" => $TaiKhoan]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    //Thêm bằng Import File 
    public function storeImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        $import = new UsersImport();
        $import->import($request->file);
        if ($import->failures()->count() == 0 && count($import->Err) == 0) {
            return response()->json(['status' => "Success"]);
        } else {
            $errors = [];
            foreach ($import->failures() as $value) {
                if (Arr::get($errors, $value->row())) {
                    $errors[$value->row()] = $errors[$value->row()] . ',' . implode(", ", $value->errors());
                } else {
                    $errors[$value->row()] = implode(", ", $value->errors());
                }
            }
            foreach ($import->Err as $value) {

                if (Arr::get($errors, $value['row'])) {
                    $errors[$value['row']] = $errors[$value['row']] . ',' . $value['err'];
                } else {
                    $errors[$value['row']] = $value['err'];
                }
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'infor' => $errors]);
        }
    }
    /**
     * Lấy 1 sinh viên
     */
    public function show($id)
    {
        try {
            $Student = DB::table('Tb_sinhvien')->join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')->where("MaSinhVien", $id)->get([
                    'MaSinhVien', 'HoTen', 'NgaySinh', 'NoiSinh', 'GioiTinh', 'DanToc',
                    'TonGiao', 'QuocTich', 'DiaChiBaoTin', 'SDT', 'Email', 'HoKhauTinh', 'HoKhauHuyen',
                    'HoKhauXaPhuong', 'TinhTrangSinhVien', 'HeDaoTao', 'TenKhoa', 'TenLop', 'SoCMTND', 'NgayCapCMTND', 'NoiCapCMTND'
                ])->first();
            if ($Student) {
                return response()->json(['status' => "Success", 'data' => $Student]);
            } else {
                return response()->json(['status' => "Failed", 'Err_Message' => 'Not Found']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => 'NotFound', 'infor' => $e->getMessage()]);
        }
    }
    /**
     * Lấy lịch sử chỉnh sửa sinh viên
     */
    public function userHistory(Request $request, $id)
    {
        $Tranfer = [
            'MaSinhVien' => "Mã sinh viên",
            'HoTen' => 'Họ và tên',
            'NgaySinh' => 'Ngày sinh',
            'NoiSinh' => 'Nơi sinh',
            'GioiTinh' => 'Giới tính',
            'DanToc' => 'Dân tộc',
            'TonGiao' => 'Tôn giáo',
            'QuocTich' => 'Quốc tịch',
            'SoCMTND' => 'Số Chứng minh nhân dân',
            'NgayCapCMTND' => 'Ngày cấp CMTND',
            'NoiCapCMTND' => 'Nơi cấp CMTND',
            'DiaChiBaoTin' => 'Địa chỉ báo tin',
            'SDT' => 'Số điện thoại',
            'Email' => 'Email',
            'HoKhauTinh' => 'Hộ khẩu tỉnh',
            'HoKhauHuyen' => 'Hộ khẩu huyện',
            'HoKhauXaPhuong' => 'Hộ khẩu xã/phường',
            'TinhTrangSinhVien' => 'Tình trạng sinh viên',
            'HeDaoTao' => 'Hệ đào tạo',
            'TenLop' => 'Tên lớp'
        ];
        try {
            $list = Tb_lichsu::where('tb_lichsu.MaSinhVien', $id)
                ->join('tb_tk_quanly', 'tb_tk_quanly.MaTK', '=', 'tb_lichsu.MaTK')
                ->join('tb_sinhvien', 'tb_sinhvien.MaSinhVien', '=', 'tb_lichsu.MaSinhVien')
                ->get(['NoiDung', 'TenDangNhap', 'ThoiGian', 'tb_lichsu.MaSinhVien']);
            $result = [];
            foreach ($list as  $item) {
                $Fields = explode(";", $item->NoiDung);
                unset($Fields[count($Fields) - 1]);
                $Context = [];
                foreach ($Fields as $value) {
                    $content = explode(":", $value);
                    if ($content[0] === "MaLop") {
                        $Lop = Tb_lop::find($content[1])->TenLop;
                        // var_dump($Lop);
                        $Context["Tên lớp"] = $Lop;
                        // var_dump($Context["TenLop"]);
                    } else {
                        $Context[$Tranfer[$content[0]]] = $content[1];
                    }
                }
                $item->NoiDung = $Context;
                $result[] = $item;
            }
            return response()->json(['status' => "Success", 'data' => $result]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    /**
     * Sửa 1 sinh viên
     */
    public function update(UpdateUser $request, $id)
    {
        $validated = $request->validated();
        try {
            // Get Ma Lop
            $TenLop = $request->TenLop;
            if ($TenLop) {
                $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
                $request->MaLop = $MaLop;
                $validated = Arr::except($request->input(), ["TenLop"]);
                $validated['MaLop'] = $MaLop;
            } else {
                return response()->json(['status' => "Failed", 'Err_Message' => "MaLop không tồn tại"]);
            }
            unset($validated['_method']);

            $user = Tb_sinhvien::find($id);
            if (!$user) {
                return response()->json(['status' => "Failed"]);
            }
            $Admin = $request->user()->MaTK;
            $NoiDung = "";
            foreach ($validated as $key => $value) {
                if (trim($value) != trim($user[$key])) {
                    $NoiDung .= $key . ":" . $value . ";";
                }
            }
            DB::transaction(function () use ($validated, $id, $Admin, $NoiDung) { // Start the transaction
                Tb_sinhvien::where('MaSinhVien', $id)->update($validated);
                if (strlen($NoiDung) > 0) {
                    Tb_lichsu::create(['NoiDung' => $NoiDung, 'MaSinhVien' => $id, 'MaTK' => $Admin]);
                }
            });
            $user = DB::table('Tb_sinhvien')->join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                ->join('Tb_khoa', 'Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')->where("MaSinhVien", $id)->get([
                    'MaSinhVien', 'HoTen', 'NgaySinh', 'NoiSinh', 'GioiTinh', 'DanToc',
                    'TonGiao', 'QuocTich', 'DiaChiBaoTin', 'SDT', 'Email', 'HoKhauTinh', 'HoKhauHuyen',
                    'HoKhauXaPhuong', 'TinhTrangSinhVien', 'HeDaoTao', 'TenKhoa', 'TenLop', 'SoCMTND', 'NgayCapCMTND', 'NoiCapCMTND'
                ])->first();
            return response()->json(['status' => "Success", 'data' => $user]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', "inf" => $e->getMessage()]);
        }
    }
    /**
     * sửa theo danh sách sinh viên
     */
    // public function updateImport(Request $request)
    // {
    //     try {
    //         Excel::import(new UpdateUserImport, $request->file, \Maatwebsite\Excel\Excel::XLSX);
    //         return response()->json(['status' => "Success"]);
    //     } catch (\Maatwebsite\Excel\Validators\ValidationException $failures) {
    //         $errors = [];
    //         foreach ($failures->failures() as $value) {
    //             $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
    //         }
    //         return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'infor' => $errors]);
    //     }
    // }
    /**
     * Lấy danh sách khoa
     */
    public function indexMajors(Request $request)
    {
        try {
            $Khoas = Tb_khoa::pluck("TenKhoa");
            return response()->json(['status' => "Success", 'data' => $Khoas]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    public function indexClass(Request $request)
    {
        try {
            $Lops = Tb_lop::pluck('TenLop');
            return response()->json(['status' => "Success", 'data' => $Lops]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    /**
     * Lấy danh sách Khóa
     */
    public function indexMajorsKey(Request $request)
    {
        try {
            $Khoas = Tb_lop::distinct('Khoas')->pluck('Khoas');
            return response()->json(['status' => "Success", 'data' => $Khoas]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    // Them danh sach khoa
    public function importMajors(Request $request)
    {
        try {
            Excel::import(new MajorImport, $request->file, \Maatwebsite\Excel\Excel::XLSX);
            return response()->json(['status' => "Success"]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $failures) {
            $errors = [];
            foreach ($failures->failures() as $value) {
                $errors[$value->row()] = implode(", ", $value->errors());
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'infor' => $errors]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    // Them Danh Sach Lop
    public function importClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        $import = new ClassImport();
        $import->import($request->file);
        if (!$import->failures()->isNotEmpty() && count($import->Err) == 0) {
            return response()->json(['status' => "Success"]);
        } else {
            $errors = [];
            foreach ($import->failures() as $value) {
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
            }
            foreach ($import->Err as $value) {
                $errors[] = ['row' => $value['row'], 'err' => [$value['err']]];
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'infor' => $errors]);
        }
    }

    /**
     * Xóa sinh viên theo mã sinh viên
     */
    public function destroy($id)
    {
        //
    }
}
