<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/books/{book}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/books/{book}/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/books/{book}/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/favorites',[FavoriteController::class,'index'])->name('favorites.index');
    Route::post('books/{book}/favorites',[FavoriteController::class,'toggle'])->name('favorites.toggle');
});

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

Route::get('/ranking', fn () => 'ランキング機能（準備中）')->name('ranking.index');
Route::get('/genres', fn () => 'ジャンル一覧（準備中）')->name('genres.index');
