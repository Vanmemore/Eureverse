@extends('layouts.app')

@section('page_title', 'Profile')

@php
    $genderColor = match($user->gender) {
        'male' => 'bg-blue-500',
        'female' => 'bg-pink-500',
        default => 'bg-gray-500'
    };
@endphp

@section('content')
<div 
    x-data="{
        showEditModal: false,
        showPostModal: false,
        showEmojiList: false,
        showEditPostId: null,
        editContent: '',
        emojis: ['😊','😂','👍','❤️','🎉'],
        startEdit(postId, content) {
            this.showEditPostId = postId;
            this.editContent = content;
            this.showEmojiList = false;
        },
        closeEditPost() {
            this.showEditPostId = null;
        }
    }"
    x-init="
        @if(session('just_updated'))
            showEditModal = false;
            showPostModal = false;
            showEditPostId = null;
        @endif
    "
    class="mt-24 px-6 flex justify-center"
>
    <div class="bg-[#212529] rounded-xl w-full max-w-2xl p-6 space-y-6">

        <!-- Profil -->
        <div class="relative flex items-center space-x-4">
            <div class="relative">
                <img src="{{ $user->photo ? route('user.photo', $user->id) : asset('img/default.png') }}"
                    class="w-20 h-20 rounded-full border-4 border-white" alt="Profile">
                <div class="absolute top-0 right-0 w-4 h-4 rounded-full border-2 border-white {{ $genderColor }}"></div>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold">{{ $user->username }}</h2>
                <p class="text-sm text-gray-300">{{ $user->name }}</p>
                <p class="text-sm mt-1 text-gray-400">{{ $user->bio ?? 'Belum ada bio.' }}</p>
            </div>
            <p class="text-sm text-gray-400 absolute top-2 right-4">Joined : {{ $user->created_at->format('d M Y') }}</p>
        </div>

        <!-- Tombol Edit -->
        <div class="text-center">
            <button @click="showEditModal = true"
                class="bg-white text-black px-6 py-1.5 rounded-lg text-sm font-semibold">
                Edit Profile
            </button>
        </div>

        <!-- Tombol Buat Post -->
        <div class="border-t border-gray-700 pt-4">
            <div class="bg-[#212529] p-4 rounded-xl shadow">
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ $user->photo ? route('user.photo', $user->id) : asset('img/default.png') }}"
                        class="w-10 h-10 rounded-full" alt="Avatar">
                    <input type="text" placeholder="Make a post....."
                        class="bg-transparent border border-gray-400 px-4 py-2 rounded w-full cursor-pointer"
                        @click="showPostModal = true" readonly>
                </div>
            </div>
        </div>

        <!-- Post History -->
        <div class="border-t border-gray-700 pt-4 space-y-4">
            @forelse($user->posts->sortByDesc('created_at') as $post)
            <div class="bg-[#374151] p-4 rounded-xl shadow space-y-2 mb-6 relative">
                @if(Auth::id() === $post->user_id)
                <div x-data="{ open: false }" class="absolute top-2 right-2">
                    <button @click="open = !open" class="text-white text-xl">⋮</button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-32 bg-white text-black rounded shadow-lg z-50 py-2 text-sm">
                        <button @click="startEdit({{ $post->id }}, `{{ addslashes($post->content) }}`)"
                            class="block px-4 py-2 hover:bg-gray-100 w-full text-left">✏️ Edit</button>
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus post ini?')" class="w-full text-left px-4 py-2 hover:bg-gray-100">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
                @endif

                <div class="flex space-x-3">
                    <img src="{{ $user->photo ? route('user.photo', $user->id) : asset('img/default.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
                    <div class="flex flex-col">
                        <div class="flex items-center space-x-2">
                            <div class="font-semibold text-sm text-white">{{ $user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $post->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <p class="text-sm text-gray-200">{{ $post->content }}</p>
                    </div>
                </div>

                @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" class="rounded-md w-full max-h-96 object-contain mt-2">
                @endif

                <div class="flex items-center justify-start space-x-4 text-sm text-gray-400 mt-2 ml-14">
                    <span>🤍 {{ $post->likes ?? 0 }}</span>
                    <span>💬 {{ $post->comments ?? 0 }}</span>
                </div>

                <!-- Modal Edit Post -->
                <div 
                    x-show="showEditPostId === {{ $post->id }}" 
                    x-cloak
                    class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center">
                    <div class="bg-[#212529] text-white w-full max-w-lg p-6 rounded-xl relative">
                        <div class="flex items-center justify-between border-b border-gray-400 pb-2 mb-3">
                            <span class="font-medium">Edit Post</span>
                            <span class="w-6"></span>
                        </div>
                        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="flex gap-4 mb-4">
                                <img src="{{ $user->photo ? route('user.photo', $user->id) : asset('img/default.png') }}"
                                    class="w-10 h-10 rounded-full mt-1" alt="Foto Profil">
                                <textarea x-model="editContent" name="content"
                                    class="w-full bg-[#343A40] text-white p-3 rounded-lg border border-gray-600 resize-none"
                                    rows="4"
                                    placeholder="Edit your post..."></textarea>
                                    @if($post->image)
                                        <div class="mt-3">
                                            <p class="text-xs text-gray-400 mb-1">Gambar sebelumnya:</p>
                                            <img src="{{ asset('storage/' . $post->image) }}" alt="Old Post Image" class="max-h-48 rounded-lg">
                                        </div>
                                    @endif
                            </div>

                            <div class="flex justify-between items-center mt-3 relative">
                                <div class="flex items-center space-x-3">
                                    <label for="editImage_{{ $post->id }}" class="cursor-pointer text-2xl">➕</label>
                                    <input type="file" name="image" id="editImage_{{ $post->id }}" class="hidden">

                                    <button type="button" @click.prevent="showEmojiList = !showEmojiList" class="text-2xl">😊</button>
                                    <div x-show="showEmojiList" x-cloak
                                        class="absolute top-full left-0 mt-2 bg-[#343A40] p-2 rounded shadow-lg flex space-x-2">
                                        <template x-for="emoji in emojis" :key="emoji">
                                            <button type="button" class="text-xl"
                                                @click="editContent += emoji; showEmojiList = false"
                                                x-text="emoji"></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button"
                                        @click="closeEditPost"
                                        class="px-4 py-1.5 rounded-md bg-gray-500 text-white text-sm">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-1.5 rounded-md bg-white text-black text-sm">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada postingan.</p>
            @endforelse
        </div>

        <!-- Modal Edit Profil -->
        @include('profiles.partials.edit-modal')

        <!-- Modal Buat Post -->
        @include('profiles.partials.post-modal')

    </div>
</div>
@endsection
