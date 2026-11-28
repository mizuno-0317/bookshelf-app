<?php

namespace Tests\Unit;

use App\Http\Requests\StoreGenreRequest;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreGenreTest extends TestCase
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
            $request, (new StoreGenreRequest)->rules()
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
            $request, (new StoreGenreRequest)->rules()
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
            $request, (new StoreGenreRequest)->rules()
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
            $request, (new StoreGenreRequest)->rules()
        );

        // Assert
        $this->assertFalse($validator->passes());
    }
}
