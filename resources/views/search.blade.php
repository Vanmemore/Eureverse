@extends('layouts.app')
@section('page_title', 'Search')

@section('content')
<div class="mt-24 px-6 flex justify-center">
    <div class="bg-[#212529] rounded-xl w-full max-w-2xl p-6 space-y-6">

        <!-- Search Bar -->
        <div class="mb-4">
            <input
                id="searchInput"
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                class="w-full bg-[#343A40] text-white px-4 py-2 rounded-lg border border-gray-700"
                placeholder="Search...">
        </div>

        <!-- List User / Hasil -->
        <div id="userList" class="divide-y divide-gray-700">
            @foreach($users as $user)
                <div class="flex items-center py-4 space-x-3">
                    <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('img/default.png') }}"
                        class="w-12 h-12 rounded-full">
                    <div class="flex-1">
                        <div class="font-bold">{{ $user->name }}</div>
                        <div class="text-sm text-gray-400">{{ '@' . $user->username }}</div>
                        <div class="text-xs text-gray-500">{{ $user->bio ?? '-' }}</div>
                    </div>
                    @auth
                        @php
                            $isFollowed = Auth::user()->following && Auth::user()->following->contains($user->id);
                        @endphp
                        @if($user->id !== Auth::id() && !$isFollowed)
                            <form method="POST" action="{{ route('follow', $user->id) }}" class="followForm" data-user="{{ $user->id }}">
                                @csrf
                                <button type="submit" class="bg-white text-black px-4 py-1 rounded">Follow</button>
                            </form>
                        @elseif($user->id !== Auth::id() && $isFollowed)
                            <span class="inline-block bg-gray-800 text-green-400 px-4 py-1 rounded mr-2">Followed</span>
                            <form method="POST" action="{{ route('unfollow', $user->id) }}" class="unfollowForm" data-user="{{ $user->id }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-gray-300 text-black px-4 py-1 rounded">Unfollow</button>
                            </form>
                        @endif
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const userList = document.getElementById('userList');
    let timer = null;

    function setHandlers() {
        // Handler Follow
        document.querySelectorAll('.followForm').forEach(form => {
            form.onsubmit = function(e) {
                e.preventDefault();
                const userId = form.dataset.user;
                fetch(`/follow/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                }).then(res => {
                    if(res.ok) triggerSearch();
                });
            }
        });
        // Handler Unfollow
        document.querySelectorAll('.unfollowForm').forEach(form => {
            form.onsubmit = function(e) {
                e.preventDefault();
                const userId = form.dataset.user;
                fetch(`/unfollow/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                }).then(res => {
                    if(res.ok) triggerSearch();
                });
            }
        });
    }

    function triggerSearch() {
        const event = new Event('input');
        searchInput.dispatchEvent(event);
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function() {
            fetch(`/search/users?q=${encodeURIComponent(searchInput.value)}`)
                .then(res => res.json())
                .then(data => {
                    userList.innerHTML = '';
                    if (data.users.length === 0) {
                        userList.innerHTML = `<div class="text-center text-gray-400 py-6">Tidak ditemukan user.</div>`;
                        return;
                    }
                    data.users.forEach(user => {
                        userList.innerHTML += `
                            <div class="flex items-center py-4 space-x-3">
                                <img src="${user.photo ? user.photo : '{{ asset('img/default.png') }}'}" class="w-12 h-12 rounded-full">
                                <div class="flex-1">
                                    <div class="font-bold">${user.name}</div>
                                    <div class="text-sm text-gray-400">@${user.username}</div>
                                    <div class="text-xs text-gray-500">${user.bio ? user.bio : '-'}</div>
                                </div>
                                ${
                                user.can_follow
                                    ? `<form class='followForm' data-user='${user.id}'>
                                            <button type='submit' class='bg-white text-black px-4 py-1 rounded'>Follow</button>
                                       </form>`
                                    : user.is_followed
                                        ? `<span class="inline-block bg-gray-800 text-green-400 px-4 py-1 rounded mr-2">Followed</span>
                                           <form class='unfollowForm' data-user='${user.id}' style="display:inline">
                                                <button type='submit' class='bg-gray-300 text-black px-4 py-1 rounded'>Unfollow</button>
                                            </form>`
                                        : ''
                                }
                            </div>
                        `;
                    });

                    // Hapus tombol follow/unfollow jika guest
                    @guest
                    document.querySelectorAll('.followForm').forEach(form => form.remove());
                    document.querySelectorAll('.unfollowForm').forEach(form => form.remove());
                    @endguest

                    // Aktifkan handler setelah render
                    @auth
                    setHandlers();
                    @endauth
                });
        }, 350);
    });

    // Handler awal jika ada user saat load page
    @auth
    setHandlers();
    @endauth
});
</script>
@endpush
