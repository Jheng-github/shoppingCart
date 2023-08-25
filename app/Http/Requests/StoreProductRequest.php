<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
                'name' => ['required', 'string', 'max:15'],
                'price' => ['required', 'integer'],
                'stock' => ['required', 'integer', 'min:0'],
                'depiction' => ['required', 'string'],
                'images' => ['required', 'array', 'max:6', 'min:1'],
                'images.*' => ['image'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '名稱不能為空',
            'price.required' => '價格不能為空',
            'price.integer' => '價格必須為整數',
            'stock.required' => '庫存不能為空',
            'stock.integer' => '庫存必須為整數',
            'stock.min' => '庫存不能小於0',
            'depiction.required' => '描述不能為空',
            'images.required' => '至少需要上傳一張圖片',
            'images.array' => '圖片必須為陣列',
            'images.max' => '最多只能上傳6張圖片',
            'images.min' => '至少需要上傳一張圖片',
            'images.*.image' => '上傳的文件必須為圖片格式',
        ];
    }
}
