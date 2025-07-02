<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%");
            })
            ->get();


        $friendsOnline = collect();   
        $friendsOffline = collect(); 

        $friendsOnline = [];
        if (Auth::check()) {
            // Asumsi punya relasi friends() di User
            $friends = Auth::user()->friends;
            $friendsOnline = $friends->where('is_online', true);
            $friendsOffline = $friends->where('is_online', false);
        }    

        return view('search', compact('users', 'friendsOnline', 'friendsOffline', 'search'));
    }

    // For AJAX
    public function searchUsers(Request $request)
    {
        $search = $request->query('q');
        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%");
            })
            ->get();


        // SearchController@searchUsers
        return response()->json([
            'users' => $users->map(function($user){
                $is_followed = Auth::check() && Auth::user()->following->contains($user->id);
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('img/default.png'),
                    'can_follow' => Auth::check() && Auth::id() !== $user->id && !$is_followed,
                    'is_followed' => $is_followed,
                ];
            })
        ]);
    }

}
