<?php

namespace Tests\Unit;

use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateGenreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい内容でバリデーションは通過する(): void
    {
        // Arrange
        $request = [
            'name' => 'テストジャンル',
        ];

        // Act
        $validator = Validator::make(
            $request, (new UpdateGenreRequest)->rules()
        );

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function ジャンル名が未入力の場合はバリデーションエラーが発生する(): void
    {
        // Arrange
        $request = [
            'name' => '',
        ];

        // Act
        $validator = Validator::make(
            $request, (new UpdateGenreRequest)->rules()
        );

        // Assert
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function ジャンル名が256文字以上の場合はバリデーションエラーが発生する(): void
    {
        // Arrange
        $request = [
            'name' => str_repeat('あ', 256),
        ];

        // Act
        $validator = Validator::make(
            $request, (new UpdateGenreRequest)->rules()
        );

        // Assert
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function 既に登録されているジャンル名の場合はバリデーションエラーが発生する(): void
    {
        // Arrange
        $genre = Genre::create([
            'name' => 'テストジャンル',
        ]);

        $request = [
            'name' => 'テストジャンル',
        ];

        // Act
        $validator = Validator::make(
            $request, (new UpdateGenreRequest)->rules()
        );

        // Assert
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function 自分自身は重複の対象とならない(): void
    {
        // Arrange
        $genre = Genre::create([
            'name' => 'テストジャンル',
        ]);

        $route = new Route('PUT', '/genres/{genre}', []);

        $request = Request::create('/genres/'.$genre->id, 'PUT');

        $route->bind($request);
        $route->setParameter('genre', $genre->id);

        $request = [
            'name' => 'テストジャンル',
        ];

        $updateRequest = UpdateGenreRequest::create(
            '/genres/'.$genre->id,
            'PUT'
        );

        $updateRequest->setRouteResolver(fn () => $route);

        // Act
        $validator = Validator::make(
            $request, $updateRequest->rules()
        );

        // Assert
        $this->assertTrue($validator->passes());
    }
}
