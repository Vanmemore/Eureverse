<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();

        if (Auth::check()) {
            $friends = Auth::user()->friends ?: collect();
            $friendsOnline = $friends->where('is_online', true);
            $friendsOffline = $friends->where('is_online', false);
        } else {
            $friendsOnline = collect();
            $friendsOffline = collect();
        }
        

        // Kirim ke view
        return view('home', compact('posts', 'friendsOnline', 'friendsOffline'));
    }
}
