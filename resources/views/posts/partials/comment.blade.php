{{-- posts/partials/comment.blade.php --}}
@php $hasLiked = $comment->likes()->where('user_id', $authId)->exists(); @endphp
<div class="mt-4 flex items-start space-x-3">
    <img src="{{ $comment->user->photo ? asset('storage/' . $comment->user->photo) : asset('img/default.png') }}"
         class="w-8 h-8 rounded-full" alt="Avatar">

    <div class="bg-[#343A40] p-3 rounded-lg w-full relative">
        {{-- Titik tiga edit/delete --}}
        @if($authId === $comment->user_id)
        <div x-data="{ openOptions: false }" class="absolute top-2 right-2">
            <button @click="openOptions = !openOptions" class="text-white text-xl">⋮</button>
            <div x-show="openOptions" @click.away="openOptions = false"
                 class="absolute right-0 mt-2 w-28 bg-white text-black rounded shadow-lg z-50 py-2 text-sm">
                <button @click="editingComment = editingComment === {{ $comment->id }} ? null : {{ $comment->id }}; openOptions = false"
                        class="block w-full text-left px-4 py-2 hover:bg-gray-100">✏️ Edit</button>
                <form action="{{ route('comments.destroy', $comment->id) }}"
                      method="POST"
                      class="delete-comment-form"
                      data-comment-id="{{ $comment->id }}"
                      onsubmit="return confirm('Yakin hapus komentar?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                        🗑 Delete
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Konten/isi komentar --}}
        <div>
            <div class="text-sm font-semibold">{{ $comment->user->name }}</div>
            <p class="text-sm mt-1 comment-content-{{ $comment->id }}">{{ $comment->content }}</p>
        </div>

        {{-- Like & Balas --}}
        <div class="flex items-center space-x-4 mt-3 text-sm">
            <form method="POST"
                  action="{{ route('comments.like', $comment->id) }}"
                  class="comment-like-form"
                  data-id="{{ $comment->id }}">
                @csrf
                <button type="button"
                        class="{{ $hasLiked ? 'text-pink-500' : 'text-gray-400' }} hover:text-pink-500">
                    ❤️ <span class="comment-like-count">{{ $comment->likes()->count() }}</span>
                </button>
            </form>

            @auth
            <button @click="replyingTo = replyingTo === {{ $comment->id }} ? null : {{ $comment->id }}"
                    class="text-blue-400 text-xs reply-btn" data-comment-id="{{ $comment->id }}">Balas</button>
            @endauth
        </div>

        {{-- Form Edit Komentar --}}
        @auth
        <div x-show="editingComment === {{ $comment->id }}" class="mt-2">
            <form action="{{ route('comments.update', $comment->id) }}"
                  method="POST"
                  class="edit-comment-form flex items-center space-x-3"
                  data-comment-id="{{ $comment->id }}">
                @csrf
                @method('PUT')
                <input type="text" name="content" value="{{ $comment->content }}"
                    class="edit-comment-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white" required>
                <button type="submit" class="bg-green-400 text-black px-3 py-1 rounded text-xs">Simpan</button>
                <button type="button" @click="editingComment = null"
                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">Cancel</button>
            </form>
        </div>
        @endauth

        {{-- Form Reply --}}
        @auth
        <div x-show="replyingTo === {{ $comment->id }}" class="mt-2">
            <form method="POST"
                  action="{{ route('comments.store', $comment->post_id) }}"
                  class="reply-form flex items-center space-x-3"
                  data-comment-id="{{ $comment->id }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('img/default.png') }}"
                     class="w-7 h-7 rounded-full" alt="Avatar">
                <input type="text" name="content"
                       class="reply-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white"
                       placeholder="Reply {{ $comment->user->name }}" required>
                <button type="submit"
                        class="bg-white text-black px-3 py-1 rounded text-xs">Reply</button>
                <button type="button"
                        @click="replyingTo = null"
                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">Cancel</button>
            </form>
        </div>
        @endauth

        {{-- Container Balasan --}}
        <div class="reply-container mt-3 space-y-2" data-replies-container="{{ $comment->id }}">
            @foreach($comment->replies as $reply)
                <div class="mt-3 bg-[#2c2f35] p-3 rounded-md">
                    <div class="flex space-x-3 items-start">
                        <img src="{{ $reply->user->photo ? asset('storage/' . $reply->user->photo) : asset('img/default.png') }}"
                             class="w-8 h-8 rounded-full" alt="Avatar">
                        <div class="flex-1">
                            <div class="text-sm font-semibold">{{ $reply->user->name }}</div>
                            <p class="text-sm text-gray-300 mt-1">{{ $reply->content }}</p>
                            <div class="flex items-center space-x-4 mt-2">
                                ❤️ {{ $reply->likes()->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
