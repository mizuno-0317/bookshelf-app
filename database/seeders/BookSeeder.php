<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 登録者を取得
        $user = User::first();

        // ジャンル一覧を取得し、ジャンル名をキーにする
        $genres = Genre::all()->keyBy('name');

        $books = [
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'published_at' => '1905-01-01',
                'isbn' => '0784101010014',
                'genres' => ['小説'],
                'description' => '夏目漱石代表作',
            ],
            [
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'published_at' => '1936-10-01',
                'isbn' => '9784422100524',
                'genres' => ['ビジネス', '自己啓発'],
                'description' => 'コミュニケーションについて解説した書籍',
            ],
            [
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell ',
                'published_at' => '2012-06-23',
                'isbn' => '9784873115658 ',
                'genres' => ['技術書'],
                'description' => 'リーダーシップについて書かれた書籍',
            ],
            [
                'title' => '７つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'published_at' => '2013-08-30',
                'isbn' => '9784863940246 ',
                'genres' => ['ビジネス', '自己啓発'],
                'description' => 'ビジネスマンがやるべき７つの習慣について解説したもの',
            ],
            [
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'published_at' => '1906-04-01',
                'isbn' => '9784101010021',
                'genres' => ['小説'],
                'description' => '夏目漱石の代表作品',
            ],
            [
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ ',
                'published_at' => '2016-09-08',
                'isbn' => '9784309226712',
                'genres' => ['歴史','科学'],
                'description' => 'サピエンスの歴史について書かれた書籍'
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'published_at' => '2017-12-18',
                'isbn' => '9784048930598',
                'genres' => ['技術書'],
                'description' => '美しいコードを書くため、コーディングを学び始めた方に読んでほしい一冊。',
            ],
            [
                'title' => ' 嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'published_at' => '2013-12-13',
                'isbn' => '9784478025819',
                'genres' => ['自己啓発'],
                'description' => '優しすぎるだけではダメ。何が大切かを見直すための本。',
            ],
            [
                'title' => '火花',
                'author' => '又吉直樹 ',
                'published_at' => '2015-03-11',
                'isbn' => '9784163902302',
                'genres' => ['小説'],
                'description' => 'お笑い芸人だった又吉が書いた、芥川賞受賞作品',
            ],
            [
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'published_at' => '2019-01-11',
                'isbn' => '9784822289607',
                'genres' => ['ビジネス', '科学'],
                'description' => 'ビジネス科学書です',
            ],
            [
                'title' => ' コンテナ物語 ',
                'author' => 'マルク・レビンソン',
                'published_at' => '2007-01-18',
                'isbn' => '9784822251468',
                'genres' => ['ビジネス', '歴史'],
                'description' => '読みやすいビジネス書です',
            ],
        ];

        // 書籍の登録
        foreach ($books as $index => $data) {
            $book = Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'published_at' => $data['published_at'],
                    'description' => $data['description'],
                    'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text='.($index + 1),
                ]
            );

            // ジャンルIDを取り出す
            $genreIds = [];
            foreach ($data['genres'] as $genreName) {
                $genreIds[] = $genres[$genreName]->id;
            }

            $book->genres()->sync($genreIds);
        }

    }
}
