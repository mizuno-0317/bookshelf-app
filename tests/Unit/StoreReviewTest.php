<?php

namespace Tests\Unit;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreReviewTest extends TestCase
{
    /** @test */
    public function 正しい入力でバリデーションは通過する(): void
    {
        // Arrange
        $request = [
            'rating' => 3,
            'comment' => '面白い本でした',
        ];
        // Act
        $validator = Validator::make(
            $request, (new StoreReviewRequest)->rules()
        );
        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 評価の入力がない場合はバリデーションは通過しない(): void
    {
        // Arrange
        $request = [
            'rating' => '',
            'comment' => '面白い本でした',
        ];
        // Act
        $validator = Validator::make(
            $request, (new StoreReviewRequest)->rules()
        );
        // Assert
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function 評価の入力が整数ではない場合はバリデーションは通過しない(): void
    {
        // Arrange
        $request = [
            'rating' => 3.5,
            'comment' => '面白い本でした',
        ];
        // Act
        $validator = Validator::make(
            $request, (new StoreReviewRequest)->rules()
        );
        // Assert
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function 評価の入力が1〜5以外の数字では場合はバリデーションは通過しない(): void
    {
        // Arrange
        $request = [
            'rating' => 6,
            'comment' => '面白い本でした',
        ];
        // Act
        $validator = Validator::make(
            $request, (new StoreReviewRequest)->rules()
        );
        // Assert
        $this->assertFalse($validator->passes());
    }

    /** @test */
    public function コメントの入力が配列の場合はバリデーションは通過しない(): void
    {
        // Arrange
        $request = [
            'rating' => 3,
            'comment' => ['面白い'],
        ];
        // Act
        $validator = Validator::make(
            $request, (new StoreReviewRequest)->rules()
        );
        // Assert
        $this->assertFalse($validator->passes());
    }
}
