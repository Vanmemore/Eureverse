@extends('layouts.app')

@section('content')
<div class="mt-24 max-w-2xl mx-auto bg-[#212529] p-6 rounded-xl text-white">
    <h2 class="text-lg font-bold mb-4 border-b border-gray-600 pb-2">Edit Postingan</h2>

    <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <textarea name="content" rows="4"
            class="w-full bg-gray-800 p-3 rounded text-white border border-gray-600"
            placeholder="Edit isi postingan...">{{ $post->content }}</textarea>

        @if ($post->image)
            <div class="mt-4">
                <p class="text-sm text-gray-400">Gambar Saat Ini:</p>
                <img src="{{ asset('storage/' . $post->image) }}" class="rounded w-full max-h-96 object-cover" alt="">
            </div>
        @endif

        <div class="mt-4">
            <label class="text-sm">Ganti Gambar (opsional)</label>
            <input type="file" name="image" class="block w-full mt-1 text-sm text-gray-300">
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-600 rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-white text-black rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
