<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 書籍一覧が取得でき、尚且つ10件ずつページネートされる(): void
    {
        // Arrange
        $this->seed();

        // Act
        $response = $this->get(route('books.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('books');

        $books = $response->viewData('books');
        $this->assertCount(10, $books->items());
    }

    /** @test */
    public function ユーザーも書籍一覧の取得ができ、尚且つ10件ずつでページネートされる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();

        // Act
        $response = $this->actingAs($user)->get(route('books.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('books');

        $books = $response->viewData('books');
        $this->assertCount(10, $books->items());
    }

    /** @test */
    public function 書籍の詳細を取得できる(): void
    {
        // Arrange
        $this->seed();
        $book = Book::first();

        // Act
        $response = $this->get(route('books.show', $book));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertViewHas('book', $book);
    }

    /** @test */
    public function ユーザーは書籍の詳細が取得できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $book = Book::first();

        // Act
        $response = $this->actingAs($user)->get(route('books.show', $book));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertViewHas('book', $book);
    }

    /** @test */
    public function ユーザーは正しい入力で書籍を登録できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $genre = Genre::first();
        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'published_date' => '2026-11-11',
            'isbn' => '9999888877711',
            'genres' => [$genre->id],
            'description' => 'テストの説明',
        ];

        // Act
        $response = $this->actingAs($user)->post(route('books.store'), $data);

        // Assert
        $response->assertSessionHasNoErrors();
        $book = Book::where('isbn', '9999888877711')->firstOrFail();
        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('books', [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'published_date' => '2026-11-11',
            'isbn' => '9999888877711',
            'description' => 'テストの説明',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);

    }

    /** @test */
    public function 未ログインユーザーは書籍登録できない(): void
    {
        // Arrange
        $this->seed();
        $genre = Genre::first();
        $data = [
            'title' => ' テスト書籍 ',
            'author' => 'テスト著者',
            'published_date' => '2026-11-11',
            'isbn' => '9999888877711',
            'genres' => $genre->id,
            'description' => 'テストの説明',
        ];

        // Act
        $response = $this->post(route('books.store'), $data);

        // Assert
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('books', [
            'isbn' => '9999888877711',
        ]);
    }

    /** @test */
    public function 入力が不正だと書籍を登録することはできない(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $genre = Genre::first();
        $data = [
            'title' => '',
            'author' => 'テスト著者',
            'published_date' => '2026-11-11',
            'isbn' => '9999888877711',
            'genres' => [$genre->id],
            'description' => 'テストの説明',
        ];

        // Act
        $response = $this->actingAs($user)
            ->from(route('books.create'))
            ->post(route('books.store'), $data);

        // Assert
        $response->assertSessionHasErrors('title');
        $response->assertRedirect(route('books.create'));
        $this->assertDatabaseMissing('books', [
            'isbn' => '9999888877711',
        ]);

    }

    /** @test */
    public function 複数登録されたジャンルを正しく取得できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $genres = Genre::whereIn('name', ['小説', '歴史'])->get();

        $data = [
            'title' => ' テスト書籍 ',
            'author' => 'テスト著者',
            'published_date' => '2026-11-11',
            'isbn' => '9999888877711',
            'genres' => $genres->pluck('id')->toArray(),
            'description' => 'テストの説明',
        ];

        // Act
        $response = $this->actingAs($user)->post(route('books.store'), $data);

        // Assert
        $book = Book::where('isbn', '9999888877711')->firstOrFail();

        $response->assertSessionHasNoErrors();

        foreach ($genres as $genre) {
            $this->assertDatabaseHas('book_genre', [
                'book_id' => $book->id,
                'genre_id' => $genre->id,
            ]);
        }
    }

    /**
     * 今後実装する予定のテスト
     * 正しい入力で更新ができるか
     * 不正な入力では更新できない
     * ユーザーは自分の登録した書籍を削除できる
     * ユーザーは自分の登録した書籍以外は削除できない
     */
}
