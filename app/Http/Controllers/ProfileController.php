<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User; 

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profiles.profile', compact('user'));
    }
    
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('profiles.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user(); // 

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'photo' => 'nullable|image|max:2048'
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->gender = $request->gender;

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::exists('public/profile/' . $user->photo)) {
                Storage::delete('public/profile/' . $user->photo);
            }

            $filename = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('public/profile', $filename);
            $user->photo = $filename;
        }

        $user->save(); 

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
