<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Register</title>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-[#1e293b] p-8 rounded-lg shadow-lg">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('img/avatar.png') }}" alt="Avatar" class="w-16 h-16">
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-2 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full mt-1 px-4 py-2 bg-transparent border rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="w-full mt-1 px-4 py-2 bg-transparent border rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('username')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full mt-1 px-4 py-2 bg-transparent border rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" name="password"
                    class="w-full mt-1 px-4 py-2 bg-transparent border rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full mt-1 px-4 py-2 bg-transparent border rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md transition duration-300">Register</button>

            <div class="mt-4 text-center text-sm text-gray-400">
                Already registered?
                <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Login</a>
            </div>
        </form>
    </div>
</body>
</html>
