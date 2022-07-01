<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MoveMilitaryLocalRequest extends FormRequest
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
            // 'SoGioiThieu'   => "required",
            'NgayCap'       => "required",
            'NoiOHienTai'   => "required",
            'NoiChuyenDen'  => "required",
            'BanChiHuy'     => "required",
            'MaSinhVien'    => "required",
            'MaSinhVien'      => "unique:tb_giay_dc_diaphuong"
        ];
    }
    public function messages()
    {
        return [
            'NgayCap.required' => 'Ngày cấp phải là duy nhất',
            'NoiOHienTai.required' => 'Nơi ở hiện tại phải là duy nhất',
            'NoiChuyenDen.required' => 'Nơi chuyển về phải là duy nhất',
            'BanChiHuy.required' => 'Ban chỉ huy phải là duy nhất',
            'MaSinhVien.unique' => 'Mã sinh viên phải là duy nhất',
            'MaSinhVien.required'=>'Mã sinh viên không được bỏ trống'
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
