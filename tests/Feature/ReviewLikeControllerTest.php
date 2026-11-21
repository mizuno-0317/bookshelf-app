<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Database\Seeders\BookSeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーはいいねできる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
            ReviewSeeder::class,
        ]);

        $user = User::first();
        $review = Review::first();
        $book = $review->book;

        // Act
        $response = $this->actingAs($user)
            ->from(route('books.show', $book))
            ->post(route('reviews.like', $review));
        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    /** @test */
    public function 未ログインユーザーはいいねできない(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
            ReviewSeeder::class,
        ]);

        $review = Review::first();

        // Act
        $response = $this->post(route('reviews.like', $review));

        // Assert
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('review_likes', [
            'review_id' => $review->id,
        ]);

    }

    /** @test */
    public function ログインユーザーはいいねを解除できる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
            ReviewSeeder::class,
        ]);

        $user = User::first();
        $review = Review::first();
        $book = $review->book;

        $user->likedReviews()->attach($review->id);

        // Act
        $response = $this->actingAs($user)
            ->from(route('books.show', $book))
            ->post(route('reviews.like', $review));

        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    /** @test */
    public function 未ログインユーザーはいいねを解除できない(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
            ReviewSeeder::class,
        ]);

        $user = User::first();
        $review = Review::first();

        $user->likedReviews()->attach($review->id);

        // Act
        $response = $this->post(route('reviews.like', $review));

        // Assert
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }
}
