<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddStudentRequest;
use App\Imports\UsersImport;
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
    public function CreateUsers($Input)
    {
        try {
            $name = explode(" ", $Input['HoTen']);
            $name = $name[count($name) - 1];
            $NgaySinh =  explode("-", $Input['NgaySinh']);
            $NgaySinh = $NgaySinh[2] . "/" . $NgaySinh[1] . "/" . $NgaySinh[0];
            return [
                'TenDangNhap' => $name . $Input["MaSinhVien"] . "@st.utc.edu.vn",
                'MatKhau' => $NgaySinh,
                'MaSinhVien' => $Input["MaSinhVien"],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    // Thêm đơn
    public function store(AddStudentRequest $request)
    {
        $validated = $request->validated();
        try {
            $TaiKhoan = $this->CreateUsers($request->safe()->only(["MaSinhVien", "NgaySinh", "HoTen"]));
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
            Excel::import(new UsersImport, $request->file);
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'err' => $e->getMessage()]);
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
    public function update(Request $request, $id)
    {
    }
    /**
     * sửa theo danh sách sinh viên
     */
    public function updateImport(Request $request, $id)
    {
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
