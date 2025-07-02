@extends('layouts.app')

@section('content')
<div class="mt-24 flex justify-center px-4">
    <div class="bg-[#212529] w-full max-w-2xl rounded-xl p-6 text-white space-y-4"
         x-data="{}" 
         x-init="
            Alpine.store('replyingTo', null);
            Alpine.store('editingComment', null);
         "
    >
        <!-- Header -->
        <div class="relative border-b border-gray-600 pb-2 mb-4">
            <button onclick="history.back()" class="absolute left-0 text-white text-xl ml-3">←</button>
            <h2 class="text-center text-xl font-bold">Post of {{ $post->user->username }}</h2>
        </div>

        <!-- Post -->
        <div class="flex space-x-3">
            <img src="{{ $post->user->photo ? asset('storage/' . $post->user->photo) : asset('img/default.png') }}"
                 class="w-10 h-10 rounded-full" alt="Avatar">
            <div>
                <div class="font-semibold">{{ $post->user->name }}</div>
                <div class="text-sm text-gray-300 mt-1">{{ $post->content }}</div>
                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image"
                         class="mt-3 rounded-md w-full max-h-96 object-contain">
                @endif
                <div class="flex space-x-6 text-sm text-gray-400 mt-2">
                    <form method="POST" action="{{ route('posts.like', $post->id) }}"
                          class="like-form" data-id="{{ $post->id }}">
                        @csrf
                        <button type="button" class="text-red-400 hover:text-pink-500">
                            ❤️ <span class="like-count">{{ $post->likes()->count() }}</span>
                        </button>
                    </form>
                    <span>💬 {{ $post->comments()->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Form Komentar Utama -->
        @auth
        <form method="POST" action="{{ route('comments.store', $post->id) }}"
              class="main-comment-form flex items-center space-x-3 mt-4"
              data-post-id="{{ $post->id }}">
            @csrf
            <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('img/default.png') }}"
                 class="w-8 h-8 rounded-full" alt="Avatar">
            <input type="text" name="content"
                   class="main-comment-input bg-[#343A40] flex-1 p-2 rounded-lg text-sm text-white placeholder-gray-400"
                   placeholder="Reply {{ $post->user->username }}" required>
            <button type="submit" class="bg-white text-black px-4 py-1.5 rounded text-sm">Post</button>
        </form>
        @endauth

        <!-- Daftar Komentar -->
        <div class="post-comments">
            @foreach($post->comments()->whereNull('parent_id')->get() as $comment)
                @php $hasLiked = $comment->likes()->where('user_id', auth()->id())->exists(); @endphp
                <div class="mt-4 flex items-start space-x-3" id="comment-{{ $comment->id }}">
                    <img src="{{ $comment->user->photo ? asset('storage/' . $comment->user->photo) : asset('img/default.png') }}"
                         class="w-8 h-8 rounded-full" alt="Avatar">

                    <div class="bg-[#343A40] p-3 rounded-lg w-full relative" x-data="{}">
                        @if(auth()->id() === $comment->user_id)
                        <div x-data="{ openOptions: false }" class="absolute top-2 right-2">
                            <button @click="openOptions = !openOptions"
                                    class="text-white text-xl">⋮</button>
                            <div x-show="openOptions"
                                 @click.away="openOptions = false"
                                 class="absolute right-0 mt-2 w-28 bg-white text-black rounded shadow-lg z-50 py-2 text-sm">
                                <button @click="Alpine.store('editingComment', {{ $comment->id }}); openOptions = false"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                                    ✏️ Edit
                                </button>
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

                        <!-- Konten Komentar -->
                        <div>
                            <div class="text-sm font-semibold">{{ $comment->user->name }}</div>
                            <p class="text-sm mt-1 comment-content-{{ $comment->id }}">{{ $comment->content }}</p>
                        </div>

                        <!-- Like & Balas -->
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
                            <button @click="Alpine.store('replyingTo', {{ $comment->id }})"
                                    class="text-blue-400 text-xs reply-btn" data-comment-id="{{ $comment->id }}">Balas</button>
                            @endauth
                        </div>

                        <!-- Form Edit -->
                        @auth
                        <div x-show="$store.editingComment === {{ $comment->id }}" x-cloak class="mt-2">
                            <form action="{{ route('comments.update', $comment->id) }}"
                                  method="POST"
                                  class="edit-comment-form flex items-center space-x-3"
                                  data-comment-id="{{ $comment->id }}">
                                @csrf
                                @method('PUT')
                                <input type="text" name="content" value="{{ $comment->content }}"
                                    class="edit-comment-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white" required>
                                <button type="submit" class="bg-green-400 text-black px-3 py-1 rounded text-xs">Simpan</button>
                                <button type="button" @click="Alpine.store('editingComment', null)"
                                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">Cancel</button>
                            </form>
                        </div>
                        @endauth

                        <!-- Form Reply -->
                        @auth
                        <div x-show="$store.replyingTo === {{ $comment->id }}" x-cloak class="mt-2">
                            <form method="POST"
                                  action="{{ route('comments.store', $post->id) }}"
                                  class="reply-form flex items-center space-x-3"
                                  data-comment-id="{{ $comment->id }}">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('img/default.png') }}"
                                     class="w-7 h-7 rounded-full" alt="Avatar">
                                <input type="text"
                                       name="content"
                                       class="reply-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white"
                                       placeholder="Reply {{ $comment->user->name }}"
                                       required>
                                <button type="submit"
                                        class="bg-white text-black px-3 py-1 rounded text-xs">
                                    Reply
                                </button>
                                <button type="button"
                                        @click="Alpine.store('replyingTo', null)"
                                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">
                                    Cancel
                                </button>
                            </form>
                        </div>
                        @endauth

                        <!-- Container Balasan -->
                        <div class="reply-container mt-3 space-y-2" data-replies-container="{{ $comment->id }}">
                            @foreach($comment->replies as $reply)
                            @php $hasReplyLiked = $reply->likes()->where('user_id', auth()->id())->exists(); @endphp
                            <div class="mt-3 bg-[#2c2f35] p-3 rounded-md" id="comment-{{ $reply->id }}" x-data="{}">
                                <div class="flex space-x-3 items-start">
                                    <img src="{{ $reply->user->photo ? asset('storage/' . $reply->user->photo) : asset('img/default.png') }}"
                                         class="w-8 h-8 rounded-full" alt="Avatar">
                                    <div class="flex-1 relative">
                                        <!-- Edit/Delete reply -->
                                        @if(auth()->id() === $reply->user_id)
                                        <div x-data="{ openOptions: false }" class="absolute top-2 right-2">
                                            <button @click="openOptions = !openOptions"
                                                    class="text-white text-xl">⋮</button>
                                            <div x-show="openOptions"
                                                 @click.away="openOptions = false"
                                                 class="absolute right-0 mt-2 w-28 bg-white text-black rounded shadow-lg z-50 py-2 text-sm">
                                                <button @click="Alpine.store('editingComment', {{ $reply->id }}); openOptions = false"
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                                                    ✏️ Edit
                                                </button>
                                                <form action="{{ route('comments.destroy', $reply->id) }}"
                                                      method="POST"
                                                      class="delete-comment-form"
                                                      data-comment-id="{{ $reply->id }}"
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
                                        <div class="text-sm font-semibold">{{ $reply->user->name }}</div>
                                        <p class="text-sm mt-1 comment-content-{{ $reply->id }}">{{ $reply->content }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <form method="POST"
                                                  action="{{ route('comments.like', $reply->id) }}"
                                                  class="comment-like-form"
                                                  data-id="{{ $reply->id }}">
                                                @csrf
                                                <button type="button"
                                                        class="{{ $hasReplyLiked ? 'text-pink-500' : 'text-gray-400' }} hover:text-pink-500">
                                                    ❤️ <span class="comment-like-count">{{ $reply->likes()->count() }}</span>
                                                </button>
                                            </form>
                                            @auth
                                            <button @click="Alpine.store('replyingTo', {{ $reply->id }})"
                                                    class="text-blue-400 text-xs reply-btn" data-comment-id="{{ $reply->id }}">Balas</button>
                                            @endauth
                                        </div>
                                        @auth
                                        <div x-show="$store.editingComment === {{ $reply->id }}" x-cloak class="mt-2">
                                            <form action="{{ route('comments.update', $reply->id) }}"
                                                  method="POST"
                                                  class="edit-comment-form flex items-center space-x-3"
                                                  data-comment-id="{{ $reply->id }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="content" value="{{ $reply->content }}"
                                                    class="edit-comment-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white" required>
                                                <button type="submit" class="bg-green-400 text-black px-3 py-1 rounded text-xs">Simpan</button>
                                                <button type="button" @click="Alpine.store('editingComment', null)"
                                                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">Cancel</button>
                                            </form>
                                        </div>
                                        <div x-show="$store.replyingTo === {{ $reply->id }}" x-cloak class="mt-2">
                                            <form method="POST"
                                                  action="{{ route('comments.store', $post->id) }}"
                                                  class="reply-form flex items-center space-x-3"
                                                  data-comment-id="{{ $reply->id }}">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                                <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('img/default.png') }}"
                                                    class="w-7 h-7 rounded-full" alt="Avatar">
                                                <input type="text"
                                                    name="content"
                                                    class="reply-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white"
                                                    placeholder="Reply {{ $reply->user->name }}"
                                                    required>
                                                <button type="submit"
                                                        class="bg-white text-black px-3 py-1 rounded text-xs">
                                                    Reply
                                                </button>
                                                <button type="button"
                                                        @click="Alpine.store('replyingTo', null)"
                                                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                        @endauth
                                        <div class="reply-container mt-3 space-y-2" data-replies-container="{{ $reply->id }}"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // AJAX Like Post
    document.querySelectorAll('.like-form').forEach(form => {
        form.addEventListener('click', async e => {
            e.preventDefault();
            const postId = form.dataset.id;
            const token  = form.querySelector('input[name="_token"]').value;
            const res    = await fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json'}
            });
            if (res.ok) {
                const data = await res.json();
                form.querySelector('.like-count').textContent = data.likes_count;
            }
        });
    });

    // AJAX Like Comment & Like Reply
    document.querySelector('.post-comments').addEventListener('click', async function(e) {
        if (e.target.closest('.comment-like-form')) {
            e.preventDefault();
            const form = e.target.closest('.comment-like-form');
            const cId = form.dataset.id;
            const token = form.querySelector('input[name="_token"]').value;
            const res = await fetch(`/comments/${cId}/like`, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json'}
            });
            if (res.ok) {
                const data = await res.json();
                form.querySelector('.comment-like-count').textContent = data.likes_count;
            }
        }
    });

    // Komentar Utama (AJAX)
    const mainForm = document.querySelector('.main-comment-form');
    if (mainForm) {
        mainForm.addEventListener('submit', async e => {
            e.preventDefault();
            const postId = mainForm.dataset.postId;
            const token  = mainForm.querySelector('input[name="_token"]').value;
            const content= mainForm.querySelector('.main-comment-input').value;
            const res    = await fetch(mainForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':token
                },
                body: JSON.stringify({ content })
            });
            if (res.ok) {
                const data = await res.json();
                const commentId = data.id;
                const isOwner = data.user_id == data.auth_id;
                const html = `
<div class="mt-4 flex items-start space-x-3" id="comment-${commentId}">
    <img src="${data.user_photo}" class="w-8 h-8 rounded-full" alt="Avatar">
    <div class="bg-[#343A40] p-3 rounded-lg w-full relative" x-data="{}">
        ${isOwner ? `
        <div x-data="{ openOptions: false }" class="absolute top-2 right-2">
            <button @click="openOptions = !openOptions" class="text-white text-xl">⋮</button>
            <div x-show="openOptions" @click.away="openOptions = false"
                class="absolute right-0 mt-2 w-28 bg-white text-black rounded shadow-lg z-50 py-2 text-sm">
                <button @click="Alpine.store('editingComment', ${commentId}); openOptions = false"
                        class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                    ✏️ Edit
                </button>
                <form action="/comments/${commentId}" method="POST"
                      class="delete-comment-form"
                      data-comment-id="${commentId}"
                      onsubmit="return confirm('Yakin hapus komentar?')">
                    <input type="hidden" name="_token" value="${data.csrf}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                        🗑 Delete
                    </button>
                </form>
            </div>
        </div>
        ` : ''}
        <div>
            <div class="text-sm font-semibold">${data.user_name}</div>
            <p class="text-sm mt-1 comment-content-${commentId}">${data.content}</p>
        </div>
        <div class="flex items-center space-x-4 mt-3 text-sm">
            <form method="POST" action="/comments/${commentId}/like" class="comment-like-form" data-id="${commentId}">
                <input type="hidden" name="_token" value="${data.csrf}">
                <button type="button" class="text-gray-400 hover:text-pink-500">
                    ❤️ <span class="comment-like-count">0</span>
                </button>
            </form>
            <button class="text-blue-400 text-xs reply-btn" data-comment-id="${commentId}">Balas</button>
        </div>
        <!-- Form Edit -->
        <div x-show="$store.editingComment === ${commentId}" x-cloak class="mt-2" style="display:none;">
            <form action="/comments/${commentId}" method="POST"
                  class="edit-comment-form flex items-center space-x-3"
                  data-comment-id="${commentId}">
                <input type="hidden" name="_token" value="${data.csrf}">
                <input type="hidden" name="_method" value="PUT">
                <input type="text" name="content" value="${data.content}"
                    class="edit-comment-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white" required>
                <button type="submit" class="bg-green-400 text-black px-3 py-1 rounded text-xs">Simpan</button>
                <button type="button" @click="Alpine.store('editingComment', null)"
                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">Cancel</button>
            </form>
        </div>
        <!-- Form Reply -->
        <div x-show="$store.replyingTo === ${commentId}" x-cloak class="mt-2" style="display:none;">
            <form method="POST"
                  action="/posts/${postId}/comments"
                  class="reply-form flex items-center space-x-3"
                  data-comment-id="${commentId}">
                <input type="hidden" name="_token" value="${data.csrf}">
                <input type="hidden" name="parent_id" value="${commentId}">
                <img src="${data.user_photo}" class="w-7 h-7 rounded-full" alt="Avatar">
                <input type="text"
                       name="content"
                       class="reply-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white"
                       placeholder="Reply ${data.user_name}"
                       required>
                <button type="submit"
                        class="bg-white text-black px-3 py-1 rounded text-xs">
                    Reply
                </button>
                <button type="button"
                        @click="Alpine.store('replyingTo', null)"
                        class="bg-gray-500 text-white px-3 py-1 rounded text-xs">
                    Cancel
                </button>
            </form>
        </div>
        <div class="reply-container mt-3 space-y-2" data-replies-container="${commentId}"></div>
    </div>
</div>
                `;
                document.querySelector('.post-comments').insertAdjacentHTML('afterbegin', html);
                if (window.Alpine && typeof Alpine.initTree === "function") {
                    Alpine.initTree(document.getElementById(`comment-${commentId}`));
                }
                mainForm.querySelector('.main-comment-input').value = '';
            }
        });
    }

    // Delegasi tombol Balas (untuk semua reply-btn, baru & lama!)
    document.querySelector('.post-comments').addEventListener('click', function(e) {
        if (e.target.classList.contains('reply-btn')) {
            e.preventDefault();
            const commentId = e.target.getAttribute('data-comment-id');
            Alpine.store('replyingTo', Number(commentId));
        }
    });

    // AJAX Reply (delegation)
    document.querySelector('.post-comments').addEventListener('submit', async function(e) {
        if (e.target.classList.contains('reply-form')) {
            e.preventDefault();
            const form     = e.target;
            const parentId = form.dataset.commentId;
            const token    = form.querySelector('input[name="_token"]').value;
            const content  = form.querySelector('.reply-input').value;
            const postId   = '{{ $post->id }}';
            const res      = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ content, parent_id: parentId })
            });
            if (res.ok) {
                const data = await res.json();
                const replyId = data.id;
                const html = `
<div class="mt-3 bg-[#2c2f35] p-3 rounded-md" id="comment-${replyId}" x-data="{}">
  <div class="flex space-x-3 items-start">
    <img src="${data.user_photo}" class="w-8 h-8 rounded-full" alt="Avatar">
    <div class="flex-1 relative">
      <div class="text-sm font-semibold">${data.user_name}</div>
      <p class="text-sm mt-1 comment-content-${replyId}">${data.content}</p>
      <div class="flex items-center space-x-4 mt-2">
        <form method="POST" action="/comments/${replyId}/like" class="comment-like-form" data-id="${replyId}">
            <input type="hidden" name="_token" value="${data.csrf}">
            <button type="button" class="text-gray-400 hover:text-pink-500">
                ❤️ <span class="comment-like-count">0</span>
            </button>
        </form>
        <button class="text-blue-400 text-xs reply-btn" data-comment-id="${replyId}">Balas</button>
      </div>
      <!-- Form Edit -->
      <div x-show="$store.editingComment === ${replyId}" x-cloak class="mt-2" style="display:none;">
        <form action="/comments/${replyId}" method="POST"
              class="edit-comment-form flex items-center space-x-3"
              data-comment-id="${replyId}">
            <input type="hidden" name="_token" value="${data.csrf}">
            <input type="hidden" name="_method" value="PUT">
            <input type="text" name="content" value="${data.content}"
                class="edit-comment-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white" required>
            <button type="submit" class="bg-green-400 text-black px-3 py-1 rounded text-xs">Simpan</button>
            <button type="button" @click="Alpine.store('editingComment', null)"
                    class="bg-gray-500 text-white px-3 py-1 rounded text-xs">Cancel</button>
        </form>
      </div>
      <!-- Form Reply -->
      <div x-show="$store.replyingTo === ${replyId}" x-cloak class="mt-2" style="display:none;">
        <form method="POST"
              action="/posts/${postId}/comments"
              class="reply-form flex items-center space-x-3"
              data-comment-id="${replyId}">
            <input type="hidden" name="_token" value="${data.csrf}">
            <input type="hidden" name="parent_id" value="${replyId}">
            <img src="${data.user_photo}" class="w-7 h-7 rounded-full" alt="Avatar">
            <input type="text"
                   name="content"
                   class="reply-input bg-[#2c2f35] flex-1 p-2 rounded-lg text-sm text-white"
                   placeholder="Reply ${data.user_name}"
                   required>
            <button type="submit"
                    class="bg-white text-black px-3 py-1 rounded text-xs">
                Reply
            </button>
            <button type="button"
                    @click="Alpine.store('replyingTo', null)"
                    class="bg-gray-500 text-white px-3 py-1 rounded text-xs">
                Cancel
            </button>
        </form>
      </div>
      <div class="reply-container mt-3 space-y-2" data-replies-container="${replyId}"></div>
    </div>
  </div>
</div>`;
                document.querySelector(`[data-replies-container="${parentId}"]`).insertAdjacentHTML('beforeend', html);
                if (window.Alpine && typeof Alpine.initTree === "function") {
                    Alpine.initTree(document.getElementById(`comment-${replyId}`));
                }
                form.querySelector('.reply-input').value = '';
                Alpine.store('replyingTo', null);
            }
        }
    });

    // AJAX Edit Komentar/Reply
    document.querySelector('.post-comments').addEventListener('submit', async function(e) {
        if (e.target.classList.contains('edit-comment-form')) {
            e.preventDefault();
            const form = e.target;
            const commentId = form.dataset.commentId;
            const token = form.querySelector('input[name="_token"]').value;
            const content = form.querySelector('.edit-comment-input').value;
            const url = form.action;

            const res = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ content })
            });

            if (res.ok) {
                const data = await res.json();
                // Update DOM komentar/reply yang diedit
                const contentElem = document.querySelector(`.comment-content-${commentId}`);
                if (contentElem) contentElem.textContent = data.content;
                Alpine.store('editingComment', null);
            }
        }
    });

    // AJAX Delete Komentar/Reply
    document.querySelector('.post-comments').addEventListener('submit', async function(e) {
        if (e.target.classList.contains('delete-comment-form')) {
            e.preventDefault();
            const form = e.target;
            const commentId = form.dataset.commentId;
            const token = form.querySelector('input[name="_token"]').value;
            const url = form.action;
            if (!confirm('Yakin hapus komentar?')) return;
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: new URLSearchParams({_method: 'DELETE'})
            });
            if (res.ok) {
                const commentDiv = document.getElementById(`comment-${commentId}`);
                if (commentDiv) commentDiv.remove();
            }
        }
    });
});
</script>
@endpush
