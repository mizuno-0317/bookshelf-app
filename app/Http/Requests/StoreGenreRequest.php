<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGenreRequest extends FormRequest
{
    /**
     * リクエストの認可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:genres,name',
        ];
    }

    /**
     * メッセージ
     */
    public function messages(): array
    {
        return [
            'name.required' => 'ジャンル名の入力は必須です',
            'name.unique' => 'このジャンル名は既に登録があります',
            'name.max' => 'ジャンル名は255文字以内で入力してください',
        ];
    }
}
