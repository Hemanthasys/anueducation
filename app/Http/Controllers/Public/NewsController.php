<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::where('status', 'published')
            ->orderBy('published_at', 'desc');

        $filter = $request->get('filter', 'all');

        match($filter) {
            'week'  => $query->where('published_at', '>=', now()->subWeek()),
            'month' => $query->where('published_at', '>=', now()->subMonth()),
            'year'  => $query->where('published_at', '>=', now()->subYear()),
            default => null,
        };

        $news = $query->paginate(12)->withQueryString();

        return view('public.news.index', compact('news', 'filter'));
    }

    public function show($slug)
    {
        $article = News::where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = News::where('status', 'published')
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('public.news.show', compact('article', 'related'));
    }
}