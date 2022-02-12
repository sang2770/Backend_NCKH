<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\Tb_tk_sinhvien;
use App\Notifications\ResetPasswordRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginClientController extends Controller
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
        if (auth()->guard('user')->attempt($input)) {
            config(['auth.guards.api.provider' => 'user']);
            $user = Tb_tk_sinhvien::find(auth()->guard('user')->user()->MaTKSV);
            $token = $user->createToken('authToken', ['user'])->accessToken;
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
    // Get User
    public function user(Request $request)
    {
        $result = ['status' => 'Success',  'user' => $request->user()];
        return response()->json($result);
    }
    // Get User
    public function change(Request $request)
    {
        // var_dump($request->user());
        $validator = Validator::make($request->input(), [
            'MaSinhVien' => 'required',
            'TenDangNhap' => 'required',
            'Old' => 'required',
            'New' => 'required',
            'New_Repeat' => "required"
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        try {
            $Id = $request->MaSinhVien;
            $Name = $request->TenDangNhap;
            $New = Hash::make($request->New);
            $user = Tb_tk_sinhvien::where('MaSinhVien', $Id)->where("TenDangNhap", $Name)->first();;
            if (!$user) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Not Found"]);
            } elseif (!Hash::check($request->Old, $user->MatKhau)) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Mật khẩu không chính xác!"]);
            } elseif ($request->New != $request->New_Repeat) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Mật khẩu không trùng khớp"]);
            } else {
                Tb_tk_sinhvien::where("MaSinhVien", $Id)->update(['MatKhau' => $New]);
                $request->user()->token()->revoke();
                return response()->json(['status' => 'Success']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'Failed', 'Err_Message' => $e->getMessage()]);
        }
    }
    // Override
    public function TenDangNhap()
    {
        return 'TenDangNhap';
    }

    public function Forgot(Request $request)
    {
        try {
            $request->validate(['email' => "required|email"]);
            $user = Tb_tk_sinhvien::where('TenDangNhap', $request->email)->firstOrFail();
            $passwordReset = PasswordReset::updateOrCreate([
                'email' => $user->TenDangNhap,
            ], [
                'token' => Str::random(60),
            ]);
            if ($passwordReset) {
                $user->notify(new ResetPasswordRequest($passwordReset->token));
            }
            return response()->json([
                'status' => "Success",
                'Message' => 'We have e-mailed your password reset link!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => "Failed",
                "Err_Message" => "Có sự cố vui vòng thử lại sau"
            ]);
        }
    }
    public function Reset(Request $request)
    {
        try {
            $passwordReset = PasswordReset::where('token', $request->token)->firstOrFail();
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
                $passwordReset->delete();
                return response()->json([
                    'status' => "Failed",
                    'Err_Message' => 'This password reset token is invalid.',
                ], 422);
            }
            $user = Tb_tk_sinhvien::where('TenDangNhap', $passwordReset->email)->firstOrFail();
            $updatePasswordUser = $user->update(['MatKhau' => Hash::make($request->MatKhau)]);
            $passwordReset->delete();

            return response()->json([
                'status' => "Success",
                'Message' => "Bạn đã thay đổi thành công vui lòng login lại.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => "Failed",
                "Err_Message" => "Not found!"
            ]);
        }
    }
}
