<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    /**レビューの投稿 */
    public function store(StoreReviewRequest $request, Book $book)
    {
        // バリデーション済みデータを取得
        $validated = $request->validated();

        $review = $book->reviews()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('book.show', $book);

    }

    /**レビュー編集画面表示 */
    public function edit(Book $book, Review $review)
    {
        abort_unless($review->book_id === $book->id, 404);
        $this->authorize('edit', $review);

        return view('reviews.edit', compact('review'));
    }

    /**レビューの更新 */
    public function update(UpdateReviewRequest $request, Book $book, Review $review)
    {
        abort_unless($review->book_id === $book->id, 404);
        $this->authorize('update', $review);
        $validated = $request->validated();
        $review->update($validated);

        return redirect()->route('book.show', $book);
    }

    /**レビューの削除 */
    public function destroy(Book $book, Review $review)
    {
        abort_unless($review->book_id === $book->id, 404);
        $this->authorize('delete', $review);
        $review->delete();

        return redirect()->route('books.show', $book);

    }
}
