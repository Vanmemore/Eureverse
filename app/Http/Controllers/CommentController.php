<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'id' => $comment->id,
                'content' => $comment->content,
                'user_name' => $comment->user->name,
                'user_photo' => $comment->user->photo
                    ? asset('storage/' . $comment->user->photo)
                    : asset('img/default.png'),
                'user_id' => $comment->user_id,
                'auth_id' => Auth::id(),
                'is_owner' => Auth::id() == $comment->user_id,
                'csrf' => csrf_token(),
            ]);
        }

        return back()->with('success', 'Komentar berhasil dikirim.');
    }

    public function like(Comment $comment)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $liked = $comment->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $comment->likes()->where('user_id', $user->id)->delete();
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
        }

        return response()->json([
            'likes_count' => $comment->likes()->count()
        ]);
    }


    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);
        $comment->update(['content' => $request->content]);
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'id' => $comment->id,
                'content' => $comment->content
            ]);
        }
        return back()->with('success', 'Komentar berhasil diperbarui!');
    }


    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }
        $comment->delete();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'id' => $comment->id]);
        }
        return back()->with('success', 'Komentar berhasil dihapus!');
    }

    
}
