<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Requests\AddStudentRequest;
use App\Http\Requests\UpdateUser;
use App\Imports\UpdateUserImport;
use App\Imports\UsersImport;
use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use App\Models\Tb_tk_sinhvien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StudentManagementController extends Controller
{
    /**
     * Lấy danh sách sinh viên
     */
    public function index()
    {
        //
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
                Tb_sinhvien::insert($validated);
                Tb_tk_sinhvien::insert($TaiKhoan);
            });
            return response()->json(['status' => "Success", 'data' => ["SinhVien" => $validated, "TaiKhoan" => $TaiKhoan]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    //Thêm bằng Import File 
    public function storeImport(Request $request)
    {
        try {
            Excel::import(new UsersImport, $request->file, \Maatwebsite\Excel\Excel::XLSX);
            return response()->json(['status' => "Success"]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $failures) {
            $errors = [];
            foreach ($failures->failures() as $value) {
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'infor' => $errors]);
        }
    }
    /**
     * Lấy 1 sinh viên
     */
    public function show($id)
    {
        // 
    }

    /**
     * Sửa 1 sinh viên
     */
    public function update(UpdateUser $request, $id)
    {
        // var_dump($request->input());
        $validated = $request->validated();
        try {
            // Get Ma Lop
            $TenLop = $request->TenLop;
            $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
            $request->MaLop = $MaLop;
            $validated = $request->safe()->except(['TenLop']);
            $validated['MaLop'] = $MaLop;
            DB::transaction(function () use ($validated, $id) { // Start the transaction
                Tb_sinhvien::where('MaSinhVien', $id)->update($validated);
            });
            return response()->json(['status' => "Success", 'data' => ["SinhVien" => $validated]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    /**
     * sửa theo danh sách sinh viên
     */
    public function updateImport(Request $request)
    {
        try {
            Excel::import(new UpdateUserImport, $request->file, \Maatwebsite\Excel\Excel::XLSX);
            return response()->json(['status' => "Success"]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $failures) {
            $errors = [];
            foreach ($failures->failures() as $value) {
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'infor' => $errors]);
        }
    }
    /**
     * Lấy danh sách khoa
     */
    public function indexMajors(Request $request, $id)
    {
    }

    /**
     * Lấy danh sách Khóa
     */
    public function indexMajorsKey(Request $request, $id)
    {
    }
    /**
     * Xóa sinh viên theo mã sinh viên
     */
    public function destroy($id)
    {
        //
    }
}
