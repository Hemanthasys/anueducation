<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Gallery;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::where('is_active', true)
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('public.gallery.index', compact('galleries'));
    }

    public function show($slug)
    {
        $gallery = Gallery::where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.gallery.show', compact('gallery'));
    }
}
