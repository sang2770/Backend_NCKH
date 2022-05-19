<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\tb_tk_quanly;
use App\Notifications\ResetPasswordRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPassWord extends Controller
{
    public function Forgot(Request $request)
    {
        try {
            $request->validate(['email' => "required|email"]);
            $user = tb_tk_quanly::where('TenDangNhap', $request->email)->firstOrFail();
            $passwordReset = PasswordReset::updateOrCreate([
                'email' => $user->TenDangNhap,
            ], [
                'token' => rand(10000, 99999),
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
                "Err_Message" => "Không tìm thấy Email hợp lệ"
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
                    'Err_Message' => 'Token không hợp lệ.',
                ], 422);
            }
            $user = tb_tk_quanly::where('TenDangNhap', $passwordReset->email)->firstOrFail();
            $updatePasswordUser = $user->update(['MatKhau' => Hash::make($request->MatKhau)]);
            $passwordReset->delete();

            return response()->json([
                'status' => "Success",
                'Message' => "Bạn đã thay đổi thành công vui lòng login lại.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => "Failed",
                "Err_Message" => "Token không hợp lệ."
            ]);
        }
    }
}
