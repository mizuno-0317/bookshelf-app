<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーは正しい内容でレビューを投稿できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $book = Book::first();
        $data = [
            'rating' => 3,
            'comment' => '面白い',
        ];

        // Act
        $response = $this->actingAs($user)->post("/books/{$book->id}/reviews", $data);

        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 3,
            'comment' => '面白い',
        ]);

    }

    /** @test */
    public function 未ログインユーザーは投稿できない(): void
    {
        // Arrange
        $this->seed();
        $book = Book::first();
        $data = [
            'rating' => 3,
            'comment' => '面白い',
        ];

        // Act
        $response = $this->post("/books/{$book->id}/reviews", $data);

        // Assert
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('reviews', [
            'book_id' => $book->id,
            'comment' => '面白い',
        ]);

    }

    /** @test */
    public function バリデーションエラー時はメッセージとともに書籍詳細画面にリダイレクトする(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $book = Book::first();
        $data = [
            'rating' => '',
            'comment' => '面白い',
        ];
        // Act
        $response = $this->actingAs($user)
            ->from(route('books.show', $book))
            ->post("/books/{$book->id}/reviews", $data);

        // Assert
        $response->assertSessionHasErrors('rating');
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('reviews', [
            'book_id' => $book->id,
            'comment' => '面白い',
        ]);

    }

    /** @test */
    public function 認証済みかつ投稿者本人の場合編集画面にアクセスできる(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();
        $user = $review->user;

        // Act
        $response = $this->actingAs($user)->get(route('reviews.edit', $review));

        // Assert
        $response->assertStatus(200);

    }

    /** @test */
    public function 未認証ユーザーはレビュー編集画面にアクセスできない(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();

        // Act
        $response = $this->get(route('reviews.edit', $review));

        // Assert
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 投稿者以外が編集画面にアクセスするとエラーになる(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();
        $user = User::create([
            'name' => '舘ひろし',
            'email' => 'tachi@example.com',
            'password' => bcrypt('password'),
        ]);
        // Act
        $response = $this->actingAs($user)->get(route('reviews.edit', $review));

        // Assert
        $response->assertStatus(403);

    }

    /** @test */
    public function 投稿者本人が正しい内容でレビューを更新できる(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();
        $user = $review->user;
        $data = [
            'rating' => 5,
            'comment' => '更新後のコメント',
        ];

        // Act
        $response = $this->actingAs($user)->put(route('reviews.update', $review), $data);

        // Assert
        $response->assertRedirect(route('books.show', $review->book));
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => '更新後のコメント',
        ]);
    }

    /** @test */
    public function 不正な内容ではレビューを更新できない(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();
        $user = $review->user;
        $data = [
            'rating' => '',
            'comment' => '更新後のコメント',
        ];
        // Act
        $response = $this->actingAs($user)
            ->from(route('reviews.edit', $review))
            ->put(route('reviews.update', $review), $data);

        // Assert
        $response->assertSessionHasErrors('rating');
        $response->assertRedirect(route('reviews.edit', $review));
        $this->assertDatabaseMissing('reviews', [
            'user_id' => $user->id,
            'comment' => '更新後のコメント',
        ]);
    }

    /** @test */
    public function 投稿者本人以外は更新できない(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();
        $user = User::create([
            'name' => '舘ひろし',
            'email' => 'tachi@example.com',
            'password' => bcrypt('password'),
        ]);
        $data = [
            'rating' => 5,
            'comment' => '更新後のコメント',
        ];

        // Act
        $response = $this->actingAs($user)
            ->put(route('reviews.update', $review), $data);

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
        ]);
    }

    /** @test */
    public function 投稿者本人であればレビューを削除でき、関係するデータも削除される(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();
        $user = $review->user;
        $book = $review->book;

        // Act
        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        // Assert
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
        $this->assertDatabaseMissing('review_likes', [
            'review_id' => $review->id,
        ]);
    }

    /** @test */
    public function 投稿者本人以外は削除できない(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();
        $user = User::create([
            'name' => '舘ひろし',
            'email' => 'tachi@example.com',
            'password' => bcrypt('password'),
        ]);

        // Act
        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
        ]);
    }

    /** @test */
    public function 未認証ユーザーは削除できない(): void
    {
        // Arrange
        $this->seed();
        $review = Review::first();

        // Act
        $response = $this->delete(route('reviews.destroy',$review));

        // Assert
        $response->assertRedirect(route('login'));
    }
}
