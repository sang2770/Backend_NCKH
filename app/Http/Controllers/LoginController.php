<?php

namespace App\Http\Controllers;

use App\Models\Tb_tk_quanly;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function Login(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'TenDangNhap' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        $input = $request->only('TenDangNhap', 'password');
        if (auth()->guard('admin')->attempt($input)) {
            config(['auth.guards.api.provider' => 'admin']);
            $user = Tb_tk_quanly::select('*')->find(auth()->guard('admin')->user()->MaTK);

            $token = $user->createToken('authToken', ['admin'])->accessToken;
            $result = ['status' => 'Success', 'Token_access' => $token, 'Token_type' => "Bearer ", 'user' => $user];

            return response()->json($result);
        } else {
            return response()->json(['status' => 'Failed', 'Err_Message' => "Tài khoản hoặc mật khẩu không đúng"]);
        }
    }
    // Đăng xuất
    public function Logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['status' => 'Success']);
    }
    public function change(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'MaTK' => 'required',
            'Old' => 'required',
            'New' => 'required',

        ]);
        // var_dump($request->input());
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        try {
            $Id = $request->MaTK;
            $New =  Hash::make($request->New);
            $user = Tb_tk_quanly::where('MaTK', $Id)->first();
            // var_dump($user);
            if (!$user) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Not Found"]);
            } elseif (!Hash::check($request->Old, $user->MatKhau)) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Mật khẩu không chính xác!"]);
            } else {
                Tb_tk_quanly::where("MaTK", $Id)->update(['MatKhau' => $New]);
                $request->user()->token()->revoke();
                return response()->json(['status' => 'Success']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'Failed', 'Err_Message' => $e->getMessage()]);
        }
    }
    public function signup(Request $request)
    {
        try {
            $Validator = Validator::make(
                $request->input(),
                [
                    "TenDangNhap" => "required|email|unique:Tb_tk_quanly",
                    "MatKhau" => "required",
                    "MatKhau_repeat" => "required"
                ],
                [
                    'TenDangNhap.required' => 'Tên đăng nhập là bắt buộc',
                    'TenDangNhap.email' => 'Tên đăng nhập phải là email',
                    'TenDangNhap.unique' => 'Tên đăng nhập là duy nhất',
                    'MatKhau.required' => 'Mật khẩu is bắt buộc',
                    'MatKhau_repeat.required' => 'MatKhau_repeat là bắt buộc',
                ]
            );
            if ($Validator->fails()) {
                // Bad Request
                return response()->json(['status' => "Failed", "Err_Message" => $Validator->errors()->first()]);
            }
            $request->MatKhau = trim($request->MatKhau);
            $request->MatKhau_repeat = trim($request->MatKhau_repeat);
            if (strcmp($request->MatKhau, $request->MatKhau_repeat) < 0) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Mật khẩu không khớp"]);
            }
            $user = Tb_tk_quanly::create([
                "TenDangNhap" => $request->TenDangNhap,
                "MatKhau" => Hash::make($request->MatKhau)
            ]);
            return response()->json(['status' => 'Success', 'data' => $user]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Failed', 'Err_Message' => $e->getMessage()]);
        }
    }
    // Get User
    public function user(Request $request)
    {
        // var_dump("Me");
        $user = $request->user();
        $result = ['status' => 'Success',  'user' => $user];
        return response()->json($result);
    }
    // Override
    public function TenDangNhap()
    {
        return 'TenDangNhap';
    }
}
