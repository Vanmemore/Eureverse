<div class="flex items-start space-x-3 mt-4">
    <img src="{{ $comment->user->photo ? asset('storage/' . $comment->user->photo) : asset('img/default.png') }}"
         class="w-8 h-8 rounded-full" alt="Avatar">
    <div class="bg-[#343A40] p-3 rounded-lg w-full">
        <div>
            <div class="text-sm font-semibold">{{ $comment->user->name }}</div>
            <p class="text-sm mt-1">{{ $comment->content }}</p>
        </div>
        {{-- Tambahkan reply, like, delete di sini sesuai struktur sebelumnya --}}
    </div>
</div>
