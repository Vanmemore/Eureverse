<!-- Modal Post -->
<div 
    x-show="showPostModal" 
    x-cloak 
    class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center"
>
    <div class="bg-[#212529] text-white w-full max-w-lg p-6 rounded-xl relative">
        <div class="flex items-center justify-between border-b border-gray-400 pb-2 mb-3">
            <span class="font-medium">New Post</span>
            
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" x-data="{ imagePreview: null }" @submit="showPostModal = false">
            @csrf
            <div class="flex gap-4">
                <img src="{{ $user->photo ? route('user.photo', $user->id) : asset('img/default.png') }}"
                    class="w-10 h-10 rounded-full mt-1" alt="">
                <div class="flex-1">
                    <textarea name="content" x-ref="ta" rows="4"
                        class="w-full bg-[#343A40] text-white p-3 rounded-lg border border-gray-600 resize-none"
                        placeholder="Make a post....."></textarea>

                    <!-- Gambar Preview -->
                    <template x-if="imagePreview">
                        <div class="mt-3">
                            <p class="text-xs text-gray-400 mb-1">Preview Gambar:</p>
                            <img :src="imagePreview" class="max-h-48 rounded-lg border border-gray-600">
                        </div>
                    </template>

                    <!-- Tools -->
                    <div class="flex justify-between items-center mt-3 relative">
                        <div class="flex items-center space-x-3">
                            <label for="imageUpload" class="cursor-pointer text-2xl">➕</label>
                            <input type="file" name="image" id="imageUpload" class="hidden"
                                @change="imagePreview = URL.createObjectURL($event.target.files[0])">

                            <button type="button" @click.prevent="showEmojiList = !showEmojiList" class="text-2xl">😊</button>
                            <div x-show="showEmojiList" x-cloak
                                class="absolute top-full left-0 mt-2 bg-[#343A40] p-2 rounded shadow-lg flex space-x-2">
                                <template x-for="emoji in emojis" :key="emoji">
                                    <button type="button" class="text-xl"
                                        @click="$refs.ta.value += emoji; showEmojiList = false"
                                        x-text="emoji"></button>
                                </template>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" @click="showPostModal = false"
                                class="px-4 py-1.5 rounded-md text-sm bg-gray-600 text-white hover:bg-gray-700">Cancel</button>
                            <button type="submit"
                                class="bg-white text-black px-4 py-1.5 rounded-md text-sm">Post</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
