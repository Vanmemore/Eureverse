@extends('layouts.app')

@section('content')
@php
    $onlineFriends = [];
    $offlineFriends = [];
@endphp

<div 
  x-data="{
    showPostModal: false,
    showEmojiList: false,
    emojis: ['😊','😂','👍','❤️','🎉'],
    imagePreview: null
  }"
>
<div class="flex space-x-6 mt-24 px-10">
    {{-- MAIN --}}
    <div class="flex-1 max-w-3xl">

        {{-- Trigger Modal --}}
        @auth
        <div class="bg-[#212529] p-4 rounded-xl shadow mb-6">
            <div class="flex items-center space-x-3">
                <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('img/default.png') }}" class="w-10 h-10 rounded-full" alt="User">
                <input
                    type="text"
                    placeholder="Make a post....."
                    class="bg-transparent border border-gray-400 px-4 py-2 rounded w-full cursor-pointer"
                    @click="showPostModal = true"
                    readonly
                >
            </div>
        </div>
        @endauth

        {{-- Post Feed --}}
        @foreach($posts as $post)
            <div 
                class="bg-[#212529] p-4 rounded-xl shadow relative hover:bg-[#2c2f35] transition mb-4"
            >
                {{-- Tombol Edit/Delete tidak ikut mengganggu --}}
                @if(Auth::id() === $post->user_id)
                <div x-data="{ open: false }" class="absolute top-2 right-2 z-50">
                    <button @click="open = !open" class="text-white text-xl">⋮</button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-32 bg-white text-black rounded shadow-lg z-50 py-2 text-sm">
                        <a href="{{ route('posts.edit', $post->id) }}" @click.stop class="block px-4 py-2 hover:bg-gray-100">✏️ Edit</a>
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" @click.stop>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100"
                                    onclick="return confirm('Yakin hapus post ini?')">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Isi Post --}}
                <div class="flex space-x-3 cursor-pointer" onclick="window.location='{{ route('posts.show', $post->id) }}'">
                    <img src="{{ $post->user->photo ? asset('storage/' . $post->user->photo) : asset('img/default.png') }}"
                        class="w-10 h-10 rounded-full" alt="Avatar">
                    <div class="flex flex-col w-full">
                        <div class="font-semibold text-sm text-white">{{ $post->user->name }}</div>
                        <p class="text-sm text-gray-200 mt-1">{{ $post->content }}</p>

                        @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}"
                            alt="Post Image"
                            class="rounded-md w-full max-h-96 object-contain mt-2">
                        @endif

                        <div class="flex space-x-6 text-sm text-gray-400 mt-2">
                            <form method="POST" action="{{ route('posts.like', $post->id) }}" 
                                class="like-form" data-id="{{ $post->id }}" 
                                onclick="event.stopPropagation()">
                                @csrf
                                <button type="button" class="text-sm text-red-400 hover:text-pink-500">
                                    ❤️ <span class="like-count">{{ $post->likes()->count() }}</span>
                                </button>
                            </form>
                            <span>💬 {{ $post->comments->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modal Post --}}
<div x-show="showPostModal" x-cloak class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50">
    <div class="bg-[#212529] text-white rounded-xl w-[600px] p-4 relative">

      {{-- Modal Header --}}
      <div class="flex items-center justify-between border-b border-gray-400 pb-2 mb-3">
        <button @click="showPostModal = false" class="text-xl">←</button>
        <span class="font-medium">New Post</span>
        <span class="w-6"></span>
      </div>

      {{-- Modal Form --}}
      <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="flex gap-4">
          <!-- Avatar -->
          @auth
            <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('img/default.png') }}" class="w-10 h-10 rounded-full mt-1" alt="User">
          @endauth

          <div class="flex-1">
            <textarea
              x-ref="ta"
              name="content"
              rows="4"
              class="w-full bg-[#343A40] text-white p-3 rounded-lg border border-gray-600 resize-none"
              placeholder="Make a post....."
            ></textarea>

            {{-- Actions --}}
            <div class="flex justify-between items-center mt-3 relative">
                {{-- Upload + Emoji --}}
                <div class="flex items-center space-x-3">
                    <!-- Tombol Upload -->
                    <label for="imageUpload" class="cursor-pointer text-2xl">➕</label>
                    <input
                        type="file"
                        name="image"
                        id="imageUpload"
                        class="hidden"
                        @change="
                            const file = $event.target.files[0];
                            if (file) {
                                imagePreview = URL.createObjectURL(file);
                            } else {
                                imagePreview = null;
                            }
                        "
                    >

                    <!-- Tombol Emoji -->
                    <button
                        type="button"
                        @click.prevent="showEmojiList = !showEmojiList"
                        class="text-2xl"
                    >😊</button>

                    <!-- Daftar Emoji -->
                    <div
                        x-show="showEmojiList"
                        x-cloak
                        class="absolute top-full left-0 mt-2 bg-[#343A40] p-2 rounded shadow-lg flex space-x-2"
                    >
                        <template x-for="emoji in emojis" :key="emoji">
                            <button
                                type="button"
                                class="text-xl"
                                @click="$refs.ta.value += emoji; showEmojiList = false"
                            ><span x-text="emoji"></span></button>
                        </template>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="flex space-x-3">
                    <button
                        type="button"
                        @click="showPostModal = false"
                        class="px-4 py-1.5 rounded-md text-sm bg-gray-500 text-white"
                    >Cancel</button>
                    <button
                        type="submit"
                        class="bg-white text-black px-4 py-1.5 rounded-md text-sm"
                    >Post</button>
                </div>
            </div>

            <!-- PREVIEW GAMBAR -->
            <template x-if="imagePreview">
                <img :src="imagePreview" class="mt-3 rounded-md w-full object-cover max-h-96" alt="Preview">
            </template>
          </div>
        </div>
      </form>
    </div>
</div>

</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.like-form').forEach(form => {
        form.addEventListener('click', async (e) => {
            e.preventDefault();

            const postId = form.getAttribute('data-id');
            const token = form.querySelector('input[name="_token"]').value;
            const url = `/posts/${postId}/like`;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    const data = await res.json();
                    form.querySelector('.like-count').textContent = data.likes_count;
                }
            } catch (err) {
                console.error('Like gagal:', err);
            }
        });
    });
});
</script>
