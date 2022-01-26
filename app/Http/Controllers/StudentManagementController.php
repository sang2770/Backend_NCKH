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
            $user = Tb_sinhvien::filter($request)->paginate($limit, ['*'], 'page', $page)->toArray();
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
        try {
            $validated = $request->validated();
            // get MaLop
            $TenLop = $request->TenLop;
            $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
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
        if (!$import->failures()->isNotEmpty() && count($import->Err) == 0) {
            return response()->json(['status' => "Success"]);
        } else {
            $errors = [];
            foreach ($import->failures() as $value) {
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
            }
            foreach ($import->Err as $value) {
                $errors[] = ['row' => $value['row'], 'err' => $value['err']];
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
            $Student = Tb_sinhvien::where('MaSinhVien', $id)->first();
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
        try {
            $list = Tb_lichsu::where('tb_lichsu.MaSinhVien', $id)
                ->join('tb_tk_quanly', 'tb_tk_quanly.MaTK', '=', 'tb_lichsu.MaTK')
                ->join('tb_sinhvien', 'tb_sinhvien.MaSinhVien', '=', 'tb_lichsu.MaSinhVien')
                ->get(['NoiDung', 'HoTen', 'TenDangNhap', 'ThoiGian', 'tb_lichsu.MaSinhVien']);
            $result = [];
            foreach ($list as  $item) {
                $Fields = explode(";", $item->NoiDung);
                unset($Fields[count($Fields) - 1]);
                $Context = [];
                foreach ($Fields as $value) {
                    $content = explode(":", $value);
                    $Context[$content[0]] = $content[1];
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
    public function update(Request $request, $id)
    {
        // var_dump($request->input());
        $validated = $request->input();
        try {
            // Get Ma Lop
            $TenLop = $request->TenLop;
            if ($TenLop) {
                $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
                $request->MaLop = $MaLop;
                $validated = $request->except(['TenLop']);
                $validated['MaLop'] = $MaLop;
            }
            $validated = $request->except(['_method']);

            $Admin = $request->user()->MaTK;
            $NoiDung = "";
            foreach ($validated as $key => $value) {
                $NoiDung .= $key . ":" . $value . ";";
            }
            DB::transaction(function () use ($validated, $id, $Admin, $NoiDung) { // Start the transaction
                Tb_lichsu::create(['NoiDung' => $NoiDung, 'MaSinhVien' => $id, 'MaTK' => $Admin]);
                Tb_sinhvien::where('MaSinhVien', $id)->update($validated);
            });
            $user = Tb_sinhvien::where('MaSinhVien', $id)->get();
            return response()->json(['status' => "Success", 'data' => ["SinhVien" => $user]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
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
            $Khoas = Tb_lop::distinct('Khoas')->value('Khoas');
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
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
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
                $errors[] = ['row' => $value['row'], 'err' => $value['err']];
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
