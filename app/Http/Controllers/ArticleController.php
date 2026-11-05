<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Store;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with(['user', 'store'])->latest()->paginate(10);

        return view('articles.index', [
            'articles' => $articles,
            'store' => null,
        ]);
    }

    public function show(Article $article)
    {
        $article->load(['user', 'store']);

        return view('articles.show', [
            'article' => $article,
            'store' => $article->store,
        ]);
    }

    /**
     * Daftar artikel milik satu toko.
     */
    public function storeIndex(Store $store)
    {
        session(['active_store_id' => $store->id]);

        $articles = $store->articles()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('articles.index', compact('articles', 'store'));
    }

    /**
     * Detail artikel, hanya boleh dibuka dari storefront pemiliknya.
     */
    public function storeShow(Store $store, Article $article)
    {
        abort_unless((int) $article->store_id === (int) $store->id, 404);

        session(['active_store_id' => $store->id]);

        $article->load('user');

        return view('articles.show', compact('article', 'store'));
    }
}
