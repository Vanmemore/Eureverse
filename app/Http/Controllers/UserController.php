<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function profile()
    {
        $user = User::with('posts')->find(Auth::id());
        return view('profiles.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user = User::find(Auth::id());

        $request->validate([
    'name' => 'required|string|max:255',
    'username' => 'nullable|string|max:255',
    'email' => 'required|email|max:255',
    'phone' => 'nullable|string|max:20',
    'gender' => 'nullable|in:male,female',
    'bio' => 'nullable|string|max:255',
    'photo' => 'nullable|image|max:2048',
    ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->bio = $request->bio;

        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('profile', 'public'); 
            $user->photo = $imagePath; 
        }


        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function photo($id)
    {
        $user = User::findOrFail($id);

        if (!$user->photo) {
            return response()->file(public_path('img/default.png')); 
        }

        return response($user->photo)->header('Content-Type', 'image/jpeg');
    }
    

    
}
