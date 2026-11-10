<?php

namespace App\Http\Controllers;

use App\Models\Book;

class FavoriteController extends Controller
{
    public function index()
    {
        $books = auth()->user()
            ->favorites()
            ->withAvg('reviews','rating')
            ->simplePaginate(10);

        return view('favorites.index',compact('books'));
    }

    public function toggle(Book $book)
    {
        auth()->user()->favorites()->toggle($book->id);

        return back();
    }
}
