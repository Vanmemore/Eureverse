<div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center">
    <div class="bg-[#1F1F1F] text-white w-full max-w-lg p-6 rounded-xl relative">
        <h2 class="text-lg font-bold mb-4 border-b border-gray-600 pb-2">Edit Profile</h2>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="flex items-center space-x-6">
                <!-- Gambar -->
                <img src="{{ $user->photo ? route('user.photo', $user->id) : asset('img/default.png') }}" class="w-20 h-20 rounded-full border-4 border-white" alt="Profile">

                <!-- Tombol Upload -->
                <div>
                    <label class="bg-white text-black px-4 py-2 rounded-md cursor-pointer inline-block text-sm">
                        Choose file
                        <input type="file" name="photo" class="hidden">
                    </label>
                </div>
            </div>

            <!-- Input Data -->
            <input type="text" name="username" value="{{ $user->username }}" placeholder="Username"
                class="w-full bg-gray-800 text-white rounded px-4 py-2">
            <input type="text" name="name" value="{{ $user->name }}" placeholder="Nama"
                class="w-full bg-gray-800 text-white rounded px-4 py-2">
            <input type="email" name="email" value="{{ $user->email }}" placeholder="Email"
                class="w-full bg-gray-800 text-white rounded px-4 py-2">
            <input type="text" name="phone" value="{{ $user->phone }}" placeholder="Nomor HP"
                class="w-full bg-gray-800 text-white rounded px-4 py-2">

            <select name="gender" class="w-full bg-gray-800 text-white rounded px-4 py-2 mt-1">
                <option value="" {{ $user->gender === null ? 'selected' : '' }}>Belum memilih</option>
                <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Pria</option>
                <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Wanita</option>
            </select>

            <!-- Bio -->
            <textarea name="bio" rows="3" placeholder="Bio..."
                class="w-full bg-gray-800 text-white rounded px-4 py-2">{{ $user->bio }}</textarea>

            <!-- Tombol -->
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="showEditModal = false"
                    class="px-4 py-2 bg-gray-600 rounded text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-white text-black rounded text-sm">Save</button>
            </div>
        </form>
    </div>
</div>
