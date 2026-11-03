<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * reviewの編集について
     */
    public function edit(User $user, Review $review): Response
    {
        return $user->id === $review->user_id
            ? Response::allow()
            : Response::deny('編集の権限がありません');
    }

    /**
     * reviewの更新について
     */
    public function update(User $user, Review $review): Response
    {
        return $user->id === $review->user_id
            ? Response::allow()
            : Response::deny('編集の権限がありません');
    }

    /**
     * reviewの削除について
     */
    public function delete(User $user, Review $review): Response
    {
        return $user->id === $review->user_id
            ? Response::allow()
            : Response::deny('削除の権限がありません');
    }
}
