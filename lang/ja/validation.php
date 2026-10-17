
<?php

return [
    'required' => ':attributeは必須項目です。',
    'string' => ':attributeは文字列で指定してください。',
    'email' => ':attributeは有効なメールアドレス形式で指定してください。',
    'max' => [
        'string' => ':attributeは:max文字以下で入力してください。',
        'numeric' => ':attributeは:max以下で指定してください。',
        'file' => ':attributeは:maxキロバイト以下にしてください。',
        'array' => ':attributeは:max個以下にしてください。',
    ],
    'min' => [
        'string' => ':attributeは:min文字以上で入力してください。',
        'numeric' => ':attributeは:min以上で指定してください。',
        'file' => ':attributeは:minキロバイト以上にしてください。',
        'array' => ':attributeは:min個以上にしてください。',
    ],
    'unique' => '入力された:attributeは既に使用されています。',
    'exists' => '選択された:attributeは無効です。',
    'date' => ':attributeは正しい日付を入力してください。',
    'url' => ':attributeは有効なURL形式で指定してください。',
    'numeric' => ':attributeは数値で指定してください。',
    'digits' => ':attributeは:digits桁で指定してください。',
    'array' => ':attributeは配列で指定してください。',
    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'title' => '書籍名',
        'author' => '著者名',
        'published_at' => '出版日',
        'image_url' => '画像URL',
        'isbn' => 'ISBN',
        'genre_ids' => 'ジャンル',
        'description' => '説明文',
    ],
];
