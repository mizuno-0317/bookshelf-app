<?php

namespace App\Policies;

use APP\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    /**
     * 書籍詳細の編集について
     */
    public function edit(User $user, Book $book): Response
    {
        return $user->id === $book->user_id
        ? Response::allow()
        : Response::deny('編集の権限がありません');
    }

    /**
     * 書籍詳細の更新について
     */
    public function update(User $user, Book $book): Response
    {
        return $user->id === $book->user_id
        ? Response::allow()
        : Response::deny('編集の権限がありません');
    }

    /**
     * 書籍詳細の削除について
     */
    public function delete(User $user, Book $book): Response
    {
        return $user->id === $book->user_id
        ? Response::allow()
        : Response::deny('削除の権限がありません');
    }
}
