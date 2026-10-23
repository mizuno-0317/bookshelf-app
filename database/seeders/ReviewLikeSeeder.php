<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = Review::all();
        $users = User::all();

        foreach ($reviews as $review) {

            // レビューにいいねするユーザーを選ぶ
            $filteredUsers = $users->filter(function ($user) use ($review) {
                return $user->id !== $review->user_id;
            });

            // いいねするユーザー数の指定
            $count = rand(0, min(3, $filteredUsers->count()));

            // 選定したユーザーのIdを取り出す・０の場合はから配列
            $userIds = $count > 0
                ? $filteredUsers->random($count)->pluck('id')->all()
                : [];

            // レビューといいねするユーザーを紐づける
            $review->reviewLikes()->syncWithoutDetaching($userIds);

        }
    }
}
