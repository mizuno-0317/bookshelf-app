<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;

class BookController extends Controller
{
    /**
     * 書籍一覧表示（認証なし）
     */
    public function index()
    {
        $books = Book::withAvg('reviews', 'rating')
            ->simplePaginate(10);

        return view('books.index', compact('books'));
    }

    /**
     * 書籍登録画面表示（認証あり）
     */
    public function create()
    {
        $genres = Genre::all();
        $bookGenreIds = [];

        return view('books.create', compact('genres', 'bookGenreIds'));
    }

    /**
     * 書籍登録処理（認証あり）
     */
    public function store(StoreBookRequest $request)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // ジャンルIDを取得し、データから除外する
        $genreIds = $validated['genres'];
        unset($validated['genres']);

        // ログインユーザーを紐づけて、書籍を登録する
        $book = $request->user()->books()->create($validated);

        // ジャンルのデータを紐付ける
        $book->genres()->sync($genreIds);

        return redirect()->route('books.show', $book);

    }

    /**
     * 詳細画面の表示
     */
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    /**
     * 編集画面の表示.
     */
    public function edit(Book $id)
    {
        $this->authorize('edit', $book);

        return view('edit.books', compact('book'));
    }

    /**
     * 書籍更新
     */
    public function update(UpdateRequest $request, Book $book)
    {
        $this->authorize('update', $book);
        $validated = $request->validated();

        $genreIds = $validated['genre_ids'];
        unset($validated['genre_ids']);

        $book->update($validated);
        $book->genres()->sync($genreIds);

        return redirect()->route('books.show', $book);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $book->delete();

        return redirect->route('books.index');
    }
}
