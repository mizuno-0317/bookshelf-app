<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * リクエストの認可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価の入力は必須です',
            'rating.integer' => '評価の入力が正しくありません',
            'rating.between' => '評価は１〜５の範囲で入力してください',
        ];
    }
}
