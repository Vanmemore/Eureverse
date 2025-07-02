<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm p-6 bg-gray-800 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 text-center">Login</h2>

        @if (session('success'))
            <div class="bg-green-500 text-white p-2 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 text-black rounded">
                @error('email') <small class="text-red-400">{{ $message }}</small> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 text-black rounded">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-2 rounded">Login</button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Daftar sekarang</a>
        </p>
    </div>
</body>
</html>
