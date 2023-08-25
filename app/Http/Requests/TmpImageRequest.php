<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TmpImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image'],
        ];
    }

    public function messages()
    {
        return [
            'images.required' => '至少需要上傳一張圖片',
            'images.array' => '圖片必須為陣列',
            'images.max' => '最多只能上傳6張圖片',
            'images.min' => '至少需要上傳一張圖片',
            'images.*.image' => '必須是圖片格式',
        ];
    }
}
