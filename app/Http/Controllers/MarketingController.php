<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function home(Request $request)
    {
        if ($request->user()) {
            return redirect()->route($request->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        $posts = BlogPost::published()->latest('published_at')->limit(3)->get();

        return view('marketing.home', compact('posts'));
    }

    public function pricing()
    {
        return view('marketing.pricing');
    }
}
