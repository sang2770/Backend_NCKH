<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UpdateMoveLocalRequest extends FormRequest
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
            // 'SoDangKy'          => "required",
            'NgayDangKy'        => "required",
            'NoiDangKy'         => "required",
            'DiaChiThuongTru'   => "required",
            'NgayNop'           => "required",
            // 'SoGioiThieu'       => "required",
            'NgayCap'           => "required",
            'NoiOHienTai'       => "required",
            'NoiChuyenDen'      => "required",
            'BanChiHuy'         => "required",
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
