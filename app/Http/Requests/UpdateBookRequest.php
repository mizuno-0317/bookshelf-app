<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_date' => 'required|string|date',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:255',
            'isbn' => [
                'required',
                'string',
                'regex:/^[0-9]{13}$/',
                Rule::unique('books', 'isbn')->ignore($this->rote('book')),
            ],
            'genre_ids' => 'required|array',
            'genre_ids.*' => 'integer|exists:genres,id',
        ];
    }

    /**
     * メッセージルール
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください',
            'author.required' => '著者を入力してください',
            'isbn.regex' => 'ISBN番号の入力が正しくありません',
            'published_at.date' => '日付の入力が正しくありません',
            'image_url.url' => 'URLで入力してください',
            'genres.required' => 'ジャンルを選択してください',

        ];
    }
}
