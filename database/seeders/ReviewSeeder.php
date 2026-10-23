<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $userIndex = 0;

        foreach ($books as $index => $book) {
            $count = ($index === 10) ? 2 : 3;

            for ($i = 0; $i < $count; $i++) {
                $user = $users[$userIndex % $users->count()];

                Review::factory()->create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                ]);

                $userIndex++;
            }
        }

    }
}
