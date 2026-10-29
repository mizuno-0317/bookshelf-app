<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**リクエストの認可 */
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
            'published_date' => 'required|date',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:255',
            'isbn' => 'required|string|regex:/^[0-9]{13}$/|unique:books,isbn',
            'genres' => 'required|array',
            'genres.*' => 'integer|exists:genres,id',
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
            'published_date.required' => '出版日を入力してください',
            'published_date.date' => '日付の入力が正しくありません',
            'image_url.url' => 'URLで入力してください',
            'genres.required' => 'ジャンルを選択してください',

        ];
    }
}
