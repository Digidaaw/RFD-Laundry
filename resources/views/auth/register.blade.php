<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Buat Akun Baru</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <!-- Nama Lengkap -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="w-full border @error('name') border-red-500 @enderror rounded-lg px-4 py-2">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required class="w-full border @error('username') border-red-500 @enderror rounded-lg px-4 py-2">
                @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Password -->
            <div class="mb-4" x-data="{ showPassword: false }">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required class="w-full border @error('password') border-red-500 @enderror rounded-lg px-4 py-2 pr-10">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">
                        <!-- Ikon Mata -->
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <!-- Role -->
            <div class="mb-6">
                <label for="role" class="block text-gray-700 font-semibold mb-2">Daftar Sebagai</label>
                <select id="role" name="role" required class="w-full border @error('role') border-red-500 @enderror rounded-lg px-4 py-2">
                    <option value="kasir" @if(old('role') == 'kasir') selected @endif>Kasir</option>
                    <option value="admin" @if(old('role') == 'admin') selected @endif>Admin</option>
                </select>
                @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                Register
            </button>

            <p class="text-center text-sm text-gray-600 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:underline">
                    Login di sini
                </a>
            </p>
        </form>
    </div>

</body>
</html>
