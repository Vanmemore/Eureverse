<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FollowController extends Controller
{
    public function store(User $user)
    {
        $follower = Auth::user();
        if ($follower->id === $user->id) {
            return response()->json(['message' => 'You cannot follow yourself.'], 400);
        }

        // Asumsi sudah ada relasi 'following()' di model User
        if (!$follower->following()->where('followed_user_id', $user->id)->exists()) {
            $follower->following()->attach($user->id);
        }

        return response()->json(['message' => 'Followed!']);
    }
    
    public function destroy(User $user)
    {
        $follower = Auth::user();
        $follower->following()->detach($user->id);

        return response()->json(['message' => 'Unfollowed!']);
    }

}
