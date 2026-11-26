<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('genres', 'name')->ignore($this->route('genre')),
            ],
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
