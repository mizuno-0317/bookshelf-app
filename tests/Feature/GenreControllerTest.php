<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Database\Seeders\GenreSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーはジャンル一覧にアクセスできる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();

        // Act
        $response = $this->actingAs($user)->get(route('genres.index'));

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function ジャンル一覧は紐付けされた書籍件数を正しく取得できる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
        ]);
        $user = User::first();
        $genre = Genre::first();
        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'published_date' => '2026-11-11',
            'isbn' => '9999888877711',
            'description' => 'テストの説明',
        ];

        $book = Book::create($data);
        $genre->books()->attach($book->id);

        // Act
        $response = $this->actingAs($user)->get(route('genres.index'));

        // Assert
        $response->assertViewHas('genres', function ($genres) use ($genre) {
            return $genres->firstWhere('id', $genre->id)->books_count === 1;
        });
    }

    /** @test */
    public function 未ログインユーザーはジャンル一覧にアクセスできない(): void
    {
        // Arrange
        $this->seed();

        // Act
        $response = $this->get(route('genres.index'));
        // Assert

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはジャンル詳細ページを表示できる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
            GenreSeeder::class,
        ]);

        $user = User::first();
        $genre = Genre::first();

        $isbns = [
            '1111111111111',
            '1111111111112',
            '1111111111122',
            '1111111111222',
            '1111111112222',
            '1111111122222',
            '1111111222222',
            '1111112222222',
            '1111122222222',
            '1111222222222',
            '1112222222222',
        ];

        $books = [];
        foreach ($isbns as $isbn) {
            $books[] = Book::create([
                'user_id' => $user->id,
                'title' => 'テスト書籍',
                'author' => 'テスト著者',
                'published_date' => '2026-11-11',
                'isbn' => $isbn,
                'description' => 'テストの説明',
            ]);
        }

        foreach ($books as $book) {
            $genre->books()->attach($book->id);
        }

        // Act
        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('books', function ($books) {
            return $books->count() === 10
            && $books->perPage() === 10;
        });
    }

    /** @test */
    public function 未ログインユーザーはジャンル詳細ページを表示できない(): void
    {
        // Arrange
        $this->seed();
        $genre = Genre::first();

        // Act
        $response = $this->get(route('genres.show', $genre));

        // Assert
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはジャンル登録画面を表示できる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
        ]);
        $user = User::first();

        // Act
        $response = $this->actingAs($user)->get(route('genres.create'));
        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 正しい入力でジャンルを登録することができる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
        ]);
        $user = User::first();
        $data = [
            'name' => 'テストジャンル',
        ];

        // Act
        $response = $this->actingAs($user)->post(route('genres.store', $data));

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', [
            'name' => 'テストジャンル',
        ]);
    }

    /** @test */
    public function 不正な入力ではジャンルを登録することはできない(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
        ]);
        $user = User::first();
        $genre = Genre::create([
            'name' => 'テストジャンル',
        ]);
        $data = [
            'name' => 'テストジャンル',
        ];

        // Act
        $response = $this->actingAs($user)
            ->from(route('genres.create'))
            ->post(route('genres.store', $data));

        // Assert
        $response->assertRedirect(route('genres.create'));
        $response->assertSessionHasErrors('name');

    }

    /** @test */
    public function 正しい入力でジャンルを更新することができる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
        ]);
        $user = User::first();
        $genre = Genre::create([
            'name' => '更新前のジャンル',
        ]);
        $data = [
            'name' => '更新後のジャンル',
        ];

        // Act
        $response = $this->actingAs($user)
            ->from(route('genres.edit', $genre))
            ->put(route('genres.update', $genre), $data);

        // Assert
        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', [
            'name' => '更新後のジャンル',
        ]);

    }

    /** @test */
    public function 不正な入力ではジャンルを更新することができない(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
        ]);
        $user = User::first();
        $genre = Genre::create([
            'name' => '更新前のジャンル',
        ]);
        $data = [
            'name' => '',
        ];

        // Act
        $response = $this->actingAs($user)
            ->from(route('genres.edit', $genre))
            ->put(route('genres.update', $genre), $data);

        // Assert
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('genres', [
            'name' => '',
        ]);
        $this->assertDatabaseHas('genres', [
            'name' => '更新前のジャンル',
        ]);
    }

    /** @test */
    public function ログインユーザーはジャンルを削除することができる(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::first();
        $genre = Genre::create([
            'name' => '削除予定のジャンル',
        ]);

        // Act
        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        // Assert
        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseMissing('genres', [
            'name' => '削除予定のジャンル',
        ]);
    }

    /** @test */
    public function 未ログインユーザーはジャンルを削除することができない(): void
    {
        // Arrange
        $genre = Genre::create([
            'name' => '削除予定のジャンル',
        ]);

        // Act
        $response = $this->delete(route('genres.destroy', $genre));

        // Assert
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('genres', [
            'name' => '削除予定のジャンル',
        ]);
    }

    /** @test */
    public function 書籍の登録があるジャンルは削除することができない(): void
    {
        // Arrange
        $this->seed([
            UserSeeder::class,
        ]);
        $user = User::first();

        $book = Book::create([
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'published_date' => '2026-11-11',
            'isbn' => '1111111111111',
            'description' => 'テストの説明',
        ]);

        $genre = Genre::create([
            'name' => 'テストジャンル',
        ]);

        $book->genres()->sync([$genre->id]);

        // Act
        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        // Assert
        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'テストジャンル',
        ]);
        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);

    }
}
