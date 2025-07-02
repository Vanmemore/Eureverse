@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Edit Profil</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        {{-- Gunakan @method jika route-nya pakai PUT --}}
        {{-- @method('PUT') --}}
        
        <div>
            <label for="name" class="block text-sm font-medium text-white">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600">
        </div>
    
        <div>
            <label for="username" class="block text-sm font-medium text-white">Username</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600">
        </div>
    
        <div>
            <label for="email" class="block text-sm font-medium text-white">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600">
        </div>
    
        <div>
            <label for="phone" class="block text-sm font-medium text-white">No. Telp</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600">
        </div>
    
        <div>
            <label for="gender" class="block text-sm font-medium text-white">Jenis Kelamin</label>
            <select name="gender"
                class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600">
                <option value="" {{ $user->gender == null ? 'selected' : '' }}>Pilih</option>
                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Pria</option>
                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Wanita</option>
            </select>
        </div>
    
        <div>
            <label for="photo" class="block text-sm font-medium text-white">Foto Profil</label>
            <input type="file" name="photo" accept="image/*"
                class="w-full p-2 rounded bg-gray-800 text-white border border-gray-600">
        </div>
    
        <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
