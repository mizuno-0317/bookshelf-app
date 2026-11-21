<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Database\Seeders\BookSeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーはお気に入り一覧画面にアクセスすることができる(): void
    {
        // Arrange
        $this->seed();

        $user = User::first();
        $book = $user->favorites()->first();

        // Act
        $response = $this->actingAs($user)->get(route('favorites.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('books', function ($books) use ($book) {
            return $books->contains('id', $book->id);
        });
    }

    /** @test */
    public function お気に入り一覧は10件ずつ表示される(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $user = User::first();
        $books = Book::all();

        $user->favorites()->attach($books->pluck('id'));

        // Act
        $response = $this->actingAs($user)->get(route('favorites.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('books', function ($books) {
            return $books->count() === 10;
        });
    }

    /** @test */
    public function お気に入り一覧は登録順に表示される(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $user = User::first();
        $books = Book::orderBy('id')->take(3)->get();

        $user->favorites()->attach($books[0]->id, [
            'created_at' => now()->subMinutes(2),
        ]);

        $user->favorites()->attach($books[1]->id, [
            'created_at' => now()->subMinute(),
        ]);

        $user->favorites()->attach($books[2]->id, [
            'created_at' => now(),
        ]);

        // Act
        $response = $this->actingAs($user)->get(route('favorites.index'));

        // Assert
        $response->assertViewHas('books', function ($viewBooks) use ($books) {
            return $viewBooks->pluck('id')->toArray() === [
                $books[2]->id,
                $books[1]->id,
                $books[0]->id,
            ];
        });
    }

    /** @test */
    public function 未ログインユーザーはお気に入り一覧にアクセスできない(): void
    {
        // Arrange
        $this->seed();

        // Act
        $response = $this->get(route('favorites.index'));

        // Assert
        $response->assertRedirect(route('login'));

    }

    /** @test */
    public function ログインユーザーはお気に入り書籍の登録ができる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $user = User::first();
        $book = Book::first();

        // Act
        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        // Assert
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /** @test */
    public function 未ログインユーザーはお気に入り書籍の登録ができない(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $book = Book::first();
        // Act
        $response = $this->post(route('favorites.toggle', $book));

        // Assert
        $response->assertRedirect(route('login'));

    }

    /** @test */
    public function ログインユーザーはお気に入り書籍の解除ができる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $user = User::first();
        $book = Book::first();
        $user->favorites()->attach($book->id);

        // Act
        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        // Assert
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

    }

    /** @test */
    public function 未ログインユーザーはお気に入り書籍の解除ができない(): void
    {
        // /Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
            BookSeeder::class,
        ]);

        $user = User::first();
        $book = Book::first();
        $user->favorites()->attach($book->id);

        // Act
        $response = $this->post(route('favorites.toggle', $book));

        // Assert
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
