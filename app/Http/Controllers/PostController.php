<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    
    use AuthorizesRequests;
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            // Simpan gambar ke storage/public/posts
            $imagePath = $request->file('image')->store('posts', 'public');
        }

       Post::create([
            'content' => $request->content,
            'image' => $imagePath,
            'likes' => 0,
            'user_id' => Auth::id(),
        ]);


        return redirect()->back()->with('success', 'Postingan berhasil dibuat!');
    }  

    public function index()
    {
        $posts = Post::with(['user', 'comments'])->latest()->get();
        return view('home', compact('posts'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post); 
        return view('posts.edit', compact('post'));
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()->back()->with('success', 'Postingan berhasil dihapus.');

    }
    
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
            $post->image = $imagePath;
        }

        $post->content = $request->content;
        $post->save();
        return redirect()->back()->with(['success' => 'Postingan diperbarui!', 'just_updated' => true]);

    }

    public function show($id)
    {
        $post = Post::with(['user', 'comments.user', 'comments.replies.user'])->findOrFail($id);
        return view('posts.show', compact('post'));
    }

    public function like(Post $post)
    {
        $user = Auth::user();

        $liked = $post->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $post->likes()->where('user_id', $user->id)->delete();
        } else {
            $post->likes()->create(['user_id' => $user->id]);
        }

        // Hitung ulang dari database
        $likesCount = $post->likes()->count();

        return response()->json([
            'likes_count' => $likesCount
        ]);
    }
    
}

