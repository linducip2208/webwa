<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::published()->with('category')->latest('published_at')->paginate(9);
        $categories = BlogCategory::withCount('posts')->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(BlogPost $post): View
    {
        abort_unless($post->is_published && $post->published_at?->lte(now()), 404);

        $post->increment('views');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }

    public function category(BlogCategory $category): View
    {
        $posts = $category->posts()->where('is_published', true)
            ->latest('published_at')->paginate(9);
        $categories = BlogCategory::withCount('posts')->get();

        return view('blog.index', compact('posts', 'categories', 'category'));
    }
}
