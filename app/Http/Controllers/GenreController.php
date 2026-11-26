<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;

class GenreController extends Controller
{
    /**
     * ジャンル一覧を表示
     */
    public function index()
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンル作成ページの表示
     */
    public function create()
    {
        return view('genres.create');
    }

    /**
     * ジャンル登録
     */
    public function store(StoreGenreRequest $request)
    {
        $validated = $request->validated();
        $genre = Genre::create($validated);

        return redirect()->route('genres.index');
    }

    /**
     * ジャンル詳細表示
     */
    public function show(Genre $genre)
    {
        $books = $genre->books()->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * ジャンル編集画面表示
     */
    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    /**
     * ジャンル更新処理
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $validated = $request->validated();
        $genre->update($validated);

        return redirect()->route('genres.index');
    }

    /**
     * ジャンル削除処理.
     */
    public function destroy(Genre $genre)
    {

        // 紐づいている書籍があるか確認する
        if ($genre->books()->exists()) {
            return redirect()->route('genres.index')
                ->with('error', '書籍が登録されているジャンルは削除出来ません');
        }
        $genre->delete();

        return redirect()->route('genres.index');
    }
}
