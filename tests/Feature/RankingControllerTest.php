<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Database\Seeders\BookSeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ランキングはゲストもアクセスできる(): void
    {
        // Arrange
        $this->seed();

        // Act
        $response = $this->get(route('ranking.index'));

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function レビューがない書籍は表示されない(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $user = User::first();
        $book = Book::first();
        $book->reviews()->create([
            'user_id' => $user->id,
            'rating' => '3',
            'comment' => '面白かった',
        ]);

        // Act
        $response = $this->get(route('ranking.index'));

        // Assert

        $response->assertViewHas('rankedBooks', function ($rankedBooks) use ($book) {
            $reviewlessBook = Book::whereDoesntHave('reviews')->first();

            return $rankedBooks->contains('id', $book->id)
                && ! $rankedBooks->contains('id', $reviewlessBook->id);
        });
    }

    /** @test */
    public function ランキングはレビュー評価の_to_p10の書籍が降順で表示される(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $books = Book::all();
        $users = User::take(3)->get();

        $ratings = [
            [5, 5, 5],
            [5, 5, 4],
            [5, 4, 4],
            [4, 4, 4],
            [4, 4, 3],
            [4, 3, 3],
            [3, 3, 3],
            [3, 3, 2],
            [3, 2, 2],
            [2, 2, 2],
            [2, 2, 1],
        ];

        foreach ($books as $index => $book) {

            foreach ($users as $userIndex => $user) {

                $rating = $ratings[$index][$userIndex];

                $book->reviews()->create([
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => 'テストレビュー',
                ]);
            }
        }

        // Act
        $response = $this->get(route('ranking.index'));

        // Assert
        $response->assertViewHas('rankedBooks', function ($rankedBooks) use ($books) {
            return $rankedBooks->pluck('id')->toArray() === [
                $books[0]->id,
                $books[1]->id,
                $books[2]->id,
                $books[3]->id,
                $books[4]->id,
                $books[5]->id,
                $books[6]->id,
                $books[7]->id,
                $books[8]->id,
                $books[9]->id,
            ];
        });
    }
}
