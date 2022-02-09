<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class CreateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "TenDangNhap" => "required|email|unique:Tb_tk_quanly",
            "MatKhau" => "required",
            "MatKhau_repeat" => "required"
        ];
    }

    public function messages()
    {
        return [
            'TenDangNhap.required' => 'Tên đăng nhập là bắt buộc',
            'TenDangNhap.email' => 'Tên đăng nhập phải là email',
            'TenDangNhap.unique' => 'Tên đăng nhập là duy nhất',
            'MatKhau.required' => 'Mật khẩu is bắt buộc',
            'MatKhau_repeat.required' => 'MatKhau_repeat là bắt buộc',
        ];
    }
    protected function failedValidation(ValidationValidator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'status' => "Failed",
                'Err_Message' => $errors,
                'status_code' => 422,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
